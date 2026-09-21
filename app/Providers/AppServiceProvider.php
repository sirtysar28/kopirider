<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureMailer();
    }

    /**
     * Override the mail configuration with the SMTP settings managed
     * from the admin panel (Settings → E-mail/SMTP).
     *
     * Runs on every request and console command, so queued/cron-sent
     * mail uses the same credentials.
     */
    protected function configureMailer(): void
    {
        try {
            if (setting('smtp_enabled') !== '1') {
                return;
            }

            $encryption = setting('smtp_encryption', 'tls');
            $fromAddress = setting('smtp_from_address') ?: config('mail.from.address');
            $fromName = setting('smtp_from_name') ?: config('mail.from.name');

            config([
                'mail.default' => 'smtp',
                'mail.from.address' => $fromAddress,
                'mail.from.name' => $fromName,
                'mail.mailers.smtp.host' => setting('smtp_host', config('mail.mailers.smtp.host')),
                'mail.mailers.smtp.port' => (int) setting('smtp_port', '587'),
                'mail.mailers.smtp.username' => setting('smtp_username'),
                'mail.mailers.smtp.password' => setting('smtp_password'),
                'mail.mailers.smtp.encryption' => $encryption === 'null' ? null : $encryption,
                'mail.mailers.smtp.timeout' => 15,
            ]);
        } catch (\Throwable) {
            // Database/cache not ready yet (e.g. first migrate) — keep .env config.
        }
    }
}
