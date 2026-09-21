<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Artisan terminal — lets staff run deployment commands from the admin
 * panel (migrate, storage:link, optimize, cache:clear, …) without SSH.
 *
 * Safety model:
 *  - Only artisan commands can be executed (no raw shell access), and the
 *    command + arguments are passed as an array / escaped with
 *    escapeshellarg, so shell injection is impossible.
 *  - Interactive / long-running commands are blocked.
 *  - Only authenticated staff can reach this controller.
 *
 * Transport chain (first that works wins — strict shared hostings usually
 * disable shell functions entirely, which is why the in-process runner
 * goes first):
 *
 *  1. Artisan::call()  — runs INSIDE the current PHP process; needs no
 *     shell function at all, works on every host.
 *  2. Symfony Process  — separate process via proc_open (cleaner isolation).
 *  3. exec / passthru / shell_exec — last-resort shell functions, each
 *     argument escaped with escapeshellarg.
 *  4. Actionable error message.
 */
class TerminalController extends Controller
{
    /** Commands that make no sense (or hang) in a one-shot web request. */
    protected const BLOCKED = [
        'tinker', 'serve', 'pail', 'repl',
        'queue:listen', 'queue:work', 'queue:restart',
        'schedule:work', 'schedule:run', 'schedule:interrupt',
        'db:monitor', 'docs', 'inspire',
    ];

    /** Commands that wipe data — require an explicit JS confirmation client-side. */
    public const DESTRUCTIVE = [
        'migrate:fresh', 'migrate:refresh', 'migrate:rollback',
        'migrate:reset', 'db:wipe', 'model:prune', 'cache:forget',
    ];

    /** @var int max runtime per command, seconds */
    protected const TIMEOUT = 600;

    /** @var string|null resolved PHP CLI binary (cached per request) */
    protected ?string $phpBinary = null;

    public function index()
    {
        return view('admin.terminal.index', [
            'phpVersion' => PHP_VERSION,
            'laravelVersion' => app()->version(),
            'blocked' => self::BLOCKED,
        ]);
    }

    public function run(Request $request): JsonResponse
    {
        $data = $request->validate([
            'command' => ['required', 'string', 'max:500'],
        ]);

        try {
            return $this->execute($data['command']);
        } catch (\Throwable $e) {
            report($e);

            // Always answer in the shape the terminal UI expects — the real
            // error becomes visible output instead of an opaque 500.
            return $this->result(
                'php artisan '.trim($data['command']),
                1,
                "SERVER ERROR: ".$e->getMessage()
            );
        }
    }

    protected function execute(string $rawCommand): JsonResponse
    {
        $args = $this->parseCommand($rawCommand);

        if ($args === null) {
            return $this->result('invalid', 1, 'Could not parse the command (check the quotes).');
        }

        if (in_array($args[0], self::BLOCKED, true)) {
            return $this->result(
                $args[0],
                1,
                "Command '{$args[0]}' is blocked — it is interactive or long-running and would time out in a web request."
            );
        }

        // Web servers often cap execution time at 30s — migrate can take longer.
        @set_time_limit(0);
        @ini_set('max_execution_time', '0');

        $start = microtime(true);

        [$output, $exitCode, $transport] = $this->runInProcess($args)
            ?? $this->runViaProcess($args)
            ?? $this->runViaShellFunction('exec', $args)
            ?? $this->runViaShellFunction('passthru', $args)
            ?? $this->runViaShellFunction('shell_exec', $args)
            ?? $this->noTransportAvailable();

        return $this->result(
            'php artisan '.implode(' ', $args),
            $exitCode,
            $output.\PHP_EOL.'— '.$transport.$this->phpVersionSuffix(),
            round((microtime(true) - $start) * 1000)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Transport 1 — in-process (works everywhere, no shell needed)
    |--------------------------------------------------------------------------
    */

    /**
     * Runs the artisan command inside THIS PHP request via Artisan::call().
     * Needs no shell function at all — the reliable path on strict hostings.
     *
     * @return array{0: string, 1: int, 2: string}|null
     */
    protected function runInProcess(array $args): ?array
    {
        $name = array_shift($args);

        $exitCode = Artisan::call($name, $this->argsToParams($args));
        $output = Artisan::output();

        return [rtrim((string) $output), (int) $exitCode, 'artisan in-process'];
    }

    /**
     * ['migrate','--force'] → command 'migrate' + ['--force' => true]
     * ['route:list','--path=api'] → ['--path' => 'api']
     * numeric keys stay positional arguments (Symfony ArrayInput format).
     *
     * @return array<string|-int, mixed>
     */
    protected function argsToParams(array $args): array
    {
        $params = [];

        foreach ($args as $arg) {
            if (str_starts_with($arg, '--')) {
                if (str_contains($arg, '=')) {
                    [$key, $value] = explode('=', $arg, 2);
                    $params[$key] = $value;
                } else {
                    $params[$arg] = true;
                }
            } elseif (str_starts_with($arg, '-')) {
                $params[$arg] = true; // short flag, e.g. -f
            } else {
                $params[] = $arg; // positional argument
            }
        }

        return $params;
    }

    /*
    |--------------------------------------------------------------------------
    | Transport 2 — separate process via proc_open (Symfony Process)
    |--------------------------------------------------------------------------
    */

    /**
     * @return array{0: string, 1: int, 2: string}|null
     */
    protected function runViaProcess(array $args): ?array
    {
        if (! $this->shellCallable('proc_open')) {
            return null;
        }

        try {
            $process = new Process(
                array_merge([$this->phpBinary(), base_path('artisan')], $args),
                base_path(),
                ['APP_ENV' => app()->environment()],
                null,
                self::TIMEOUT
            );

            $process->run();

            return [$process->getOutput().$process->getErrorOutput(), (int) ($process->getExitCode() ?? 0), 'proc_open'];
        } catch (\Throwable) {
            // Broken binary / host restriction — try the next transport.
            return null;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Transport 3 — plain shell functions (each arg escaped)
    |--------------------------------------------------------------------------
    */

    /**
     * @param  'exec'|'passthru'|'shell_exec'  $fn
     * @return array{0: string, 1: int, 2: string}|null
     */
    protected function runViaShellFunction(string $fn, array $args): ?array
    {
        if (! $this->shellCallable($fn)) {
            return null;
        }

        $parts = array_merge(
            [escapeshellarg($this->phpBinary()), escapeshellarg(base_path('artisan'))],
            array_map(fn ($a) => escapeshellarg((string) $a), $args)
        );

        $command = implode(' ', $parts).' 2>&1';

        try {
            if ($fn === 'exec') {
                $lines = [];
                $exitCode = 1;
                \exec($command, $lines, $exitCode);

                return [implode(\PHP_EOL, $lines), (int) $exitCode, 'exec()'];
            }

            if ($fn === 'passthru') {
                $exitCode = 1;
                ob_start();
                passthru($command, $exitCode);

                return [(string) ob_get_clean(), (int) $exitCode, 'passthru()'];
            }

            $raw = \shell_exec($command);

            return [(string) $raw, $raw === null ? 1 : 0, 'shell_exec()'];
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @return array{0: string, 1: int, 2: string}
     */
    protected function noTransportAvailable(): array
    {
        return [
            "Could not execute the command.\n".
            "This should not happen — the in-process runner needs no shell functions.\n".
            "Please report this output to the developer.",
            1,
            'blocked',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Environment helpers
    |--------------------------------------------------------------------------
    */

    /**
     * True only when the function exists AND is not disabled by the host.
     * (On PHP 8 disabled functions are "undefined" — but some hardened
     * setups keep them listed while calls still fail, so check both.)
     */
    protected function shellCallable(string $function): bool
    {
        if (! function_exists($function)) {
            return false;
        }

        $disabled = array_map('trim', explode(',', (string) ini_get('disable_functions')));

        if (in_array($function, $disabled, true)) {
            return false;
        }

        $suhosin = array_map('trim', explode(',', (string) ini_get('suhosin.executor.func.blacklist')));

        return ! in_array($function, $suhosin, true);
    }

    /**
     * Find a PHP CLI binary that actually works. PHP_BINARY can point at
     * php-fpm on some hosting stacks, so we verify it and try alternatives.
     * Only used by the shell transports — never probe without a transport.
     */
    protected function phpBinary(): string
    {
        if ($this->phpBinary !== null) {
            return $this->phpBinary;
        }

        $candidates = array_filter(array_unique([
            PHP_BINARY ?: null,
            (new PhpExecutableFinder)->find(false),
            getenv('PHP_PATH') ?: null,
            '/usr/local/bin/php',
            '/usr/bin/php',
            'php', // last resort: whatever is in PATH
        ]));

        foreach ($candidates as $candidate) {
            if (! is_string($candidate) || $candidate === '') {
                continue;
            }

            // Bare "php" only works if something is in PATH — trust it last.
            if (str_contains($candidate, DIRECTORY_SEPARATOR) && ! is_executable($candidate)) {
                continue;
            }

            if ($this->probeBinary($candidate)) {
                return $this->phpBinary = $candidate;
            }
        }

        return $this->phpBinary = 'php';
    }

    /**
     * Verify a binary answers `php -v` using whichever transport exists.
     */
    protected function probeBinary(string $binary): bool
    {
        try {
            if ($this->shellCallable('proc_open')) {
                $p = new Process([$binary, '-v'], null, null, null, 10);

                return $p->run() === 0;
            }

            if ($this->shellCallable('exec')) {
                $out = [];
                $code = 1;
                \exec(escapeshellarg($binary).' -v 2>&1', $out, $code);

                return $code === 0;
            }
        } catch (\Throwable) {
            return false;
        }

        // No shell available — cannot verify; accept without proof.
        return true;
    }

    /**
     * Footer shown under each run: transport + CLI PHP version when we can
     * probe one, else the running (web SAPI) version. Never throws.
     */
    protected function phpVersionSuffix(): string
    {
        if (! $this->shellCallable('proc_open') && ! $this->shellCallable('exec')) {
            return ' · PHP '.PHP_VERSION.' (web)';
        }

        try {
            if ($this->shellCallable('proc_open')) {
                $p = new Process([$this->phpBinary(), '-v'], null, null, null, 10);
                $p->run();
                $raw = $p->getOutput();
            } else {
                $lines = [];
                \exec(escapeshellarg($this->phpBinary()).' -v 2>&1', $lines);
                $raw = implode("\n", $lines);
            }

            if (preg_match('/PHP (\S+)/', $raw, $m)) {
                return ' · PHP '.$m[1];
            }
        } catch (\Throwable) {
            // fall through to the web version below
        }

        return ' · PHP '.PHP_VERSION.' (web)';
    }

    /*
    |--------------------------------------------------------------------------
    | Parsing & response
    |--------------------------------------------------------------------------
    */

    /**
     * "php artisan migrate --force" | "artisan migrate" | "migrate --force"
     * → ['migrate', '--force']   (quote-aware, no shell involved)
     *
     * @return list<string>|null
     */
    protected function parseCommand(string $input): ?array
    {
        $input = trim(preg_replace('/\s+/', ' ', $input));

        // Accept the "php artisan …" and "artisan …" prefixes people type out of habit.
        $input = preg_replace('/^(php\s+)?artisan\s+/i', '', $input);
        $input = trim((string) $input);

        if ($input === '') {
            return null;
        }

        if (! preg_match('/^[\w:.\-]+/', $input)) {
            return null;
        }

        $args = array_values(array_filter(
            str_getcsv($input, ' ', "'", ''),
            fn ($a) => $a !== ''
        ));

        return $args === [] ? null : $args;
    }

    protected function result(string $command, int $exitCode, string $output, float $durationMs = 0): JsonResponse
    {
        return response()->json([
            'command' => $command,
            'output' => rtrim($output) !== '' ? rtrim($output) : '(no output)',
            'exitCode' => $exitCode,
            'durationMs' => $durationMs,
            'ok' => $exitCode === 0,
        ]);
    }
}
