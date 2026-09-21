@extends('layouts.admin')

@section('title', 'Terminal')

@section('content')
<div class="page-head">
  <div>
    <h1>🖥️ Artisan terminal</h1>
    <p class="muted">
      Run deployment commands without SSH — <code>migrate</code>, <code>storage:link</code>,
      <code>optimize</code> and friends. Prefix <code>php artisan</code> is optional.
    </p>
  </div>
  <div class="actions">
    <span class="badge confirmed">PHP {{ $phpVersion }}</span>
    <span class="badge partial">Laravel {{ $laravelVersion }}</span>
    <span class="badge booked">{{ app()->environment() }}</span>
  </div>
</div>

<div class="card terminal-card">
  <div class="term-toolbar">
    <div class="term-dots"><span></span><span></span><span></span></div>
    <b>kopi-rider — artisan</b>
    <span class="term-hint">run as: <code>php artisan &lt;command&gt;</code></span>
    <button type="button" id="termClear" class="btn btn-sm btn-line">Clear screen</button>
  </div>

  <div class="term-body" id="termBody" aria-live="polite">
    <div class="term-line muted-line">
      Kopi Rider artisan terminal — type <code>migrate --force</code>,
      <code>storage:link</code>, <code>optimize</code> or tap a quick command below.
    </div>
  </div>

  <form class="term-input" id="termForm" autocomplete="off">
    <span class="term-prompt">php&nbsp;artisan</span>
    <input type="text" id="termCommand" placeholder="migrate --force" maxlength="500"
           spellcheck="false" autocapitalize="off" required>
    <button type="submit" class="btn btn-sm" id="termRun">Run ⏎</button>
  </form>
</div>

<div class="card">
  <h2 style="font-size:17px;margin-bottom:12px;">⚡ Quick commands</h2>
  <div class="quick-grid">
    <button type="button" class="quick" data-cmd="migrate --force">
      <b>⬆️ migrate --force</b><span>apply new database migrations</span>
    </button>
    <button type="button" class="quick" data-cmd="storage:link">
      <b>🔗 storage:link</b><span>create the public/storage symlink</span>
    </button>
    <button type="button" class="quick" data-cmd="optimize">
      <b>🚀 optimize</b><span>cache config, routes &amp; views (after adding features)</span>
    </button>
    <button type="button" class="quick" data-cmd="optimize:clear">
      <b>🧹 optimize:clear</b><span>clear all caches</span>
    </button>
    <button type="button" class="quick" data-cmd="migrate:status">
      <b>📋 migrate:status</b><span>see which migrations have run</span>
    </button>
    <button type="button" class="quick" data-cmd="cache:clear">
      <b>💾 cache:clear</b><span>flush the application cache</span>
    </button>
    <button type="button" class="quick" data-cmd="config:clear">
      <b>⚙️ config:clear</b><span>remove cached config file</span>
    </button>
    <button type="button" class="quick" data-cmd="route:list">
      <b>🗺️ route:list</b><span>list all registered routes</span>
    </button>
    <button type="button" class="quick" data-cmd="about">
      <b>🔍 about</b><span>environment check (PHP, cache, DB driver)</span>
    </button>
  </div>
  <p class="muted" style="margin-top:14px;">
    ⚠️ Blocked for safety (interactive / long-running):
    @foreach ($blocked as $cmd)<code>{{ $cmd }}</code>@if (! $loop->last), @endif @endforeach.
    Destructive commands (migrate:fresh, db:wipe…) ask for confirmation first.
  </p>
</div>
@endsection

@push('scripts')
<script>
  (function () {
    var form = document.getElementById('termForm');
    var input = document.getElementById('termCommand');
    var body = document.getElementById('termBody');
    var runBtn = document.getElementById('termRun');
    var DESTRUCTIVE = ['migrate:fresh', 'migrate:refresh', 'migrate:rollback', 'migrate:reset', 'db:wipe', 'model:prune'];
    var history = JSON.parse(localStorage.getItem('terminalHistory') || '[]');
    var historyIndex = history.length;

    function esc(s) {
      var d = document.createElement('div');
      d.textContent = s == null ? '' : String(s);
      return d.innerHTML;
    }

    function print(html, cls) {
      var line = document.createElement('div');
      line.className = 'term-line ' + (cls || '');
      line.innerHTML = html;
      body.appendChild(line);
      body.scrollTop = body.scrollHeight;
      return line;
    }

    function runCommand(raw) {
      var cmd = raw.trim().replace(/^(php\s+)?artisan\s+/i, '');

      if (!cmd) return;

      // safety confirmation for destructive commands
      var base = cmd.split(/\s+/)[0].toLowerCase();
      if (DESTRUCTIVE.indexOf(base) !== -1) {
        if (!window.confirm('⚠️ "' + cmd + '" can DELETE DATA or undo migrations.\nAre you absolutely sure?')) {
          print('aborted — ' + esc(cmd), 'err');
          return;
        }
      }

      history.push(raw.trim());
      if (history.length > 50) history = history.slice(-50);
      localStorage.setItem('terminalHistory', JSON.stringify(history));
      historyIndex = history.length;

      print('<span class="p">$</span> php artisan ' + esc(cmd), 'cmd');
      var spinner = print('<span class="blink">▌</span> running…', 'out');

      runBtn.disabled = true;
      input.disabled = true;

      fetch('{{ route('admin.terminal.run') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ command: cmd })
      })
        .then(function (r) { return r.text().then(function (t) { return { status: r.status, ok: r.ok, text: t }; }); })
        .then(function (res) {
          spinner.remove();

          var j = null;
          try { j = JSON.parse(res.text); } catch (e) {}

          // Real terminal response
          if (j && typeof j.output !== 'undefined') {
            var meta = [];
            if (typeof j.durationMs === 'number') meta.push((j.durationMs / 1000).toFixed(2) + 's');
            if (typeof j.exitCode === 'number') meta.push('exit ' + j.exitCode);
            print('<pre>' + esc(j.output) + '</pre>' +
                  (meta.length ? '<div class="term-meta">✓ ' + meta.join(' · ') + '</div>' : ''),
                  j.ok ? 'out ok' : 'out err');
            return;
          }

          // Laravel rejected the request (419 CSRF, 422 validation, 500, …)
          var reason = (j && j.message) ? j.message : (res.text || '').replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim().slice(0, 300);
          var hint = '';
          if (res.status === 419 || res.status === 401) {
            hint = '\n\n→ Your session expired. Reload this page (F5) and try again.';
          } else if (res.status === 422) {
            hint = '\n\n→ Invalid input — type a command like: migrate --force';
          }
          print('HTTP ' + res.status + ' — ' + esc(reason || 'Unknown server error') + esc(hint), 'err');
        })
        .catch(function (e) {
          spinner.remove();
          print('Request failed: ' + esc(e.message) + '\n\n→ Check your internet connection, then reload the page.', 'err');
        })
        .finally(function () {
          runBtn.disabled = false;
          input.disabled = false;
          input.focus();
        });
    }

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      runCommand(input.value);
      input.value = '';
    });

    // command history with ↑ / ↓
    input.addEventListener('keydown', function (e) {
      if (e.key !== 'ArrowUp' && e.key !== 'ArrowDown') return;
      e.preventDefault();
      if (e.key === 'ArrowUp' && historyIndex > 0) {
        historyIndex--;
        input.value = history[historyIndex] || '';
      } else if (e.key === 'ArrowDown') {
        historyIndex++;
        input.value = history[historyIndex] || '';
        if (historyIndex > history.length) historyIndex = history.length;
      }
    });

    document.querySelectorAll('.quick').forEach(function (btn) {
      btn.addEventListener('click', function () {
        runCommand(btn.dataset.cmd);
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });
    });

    document.getElementById('termClear').addEventListener('click', function () {
      body.innerHTML = '<div class="term-line muted-line">screen cleared</div>';
      input.focus();
    });

    input.focus();
  })();
</script>
@endpush
