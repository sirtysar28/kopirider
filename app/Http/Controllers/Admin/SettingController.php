<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\TestMail;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * General website settings (text inputs).
     */
    public const GENERAL = [
        'whatsapp_number' => 'WhatsApp number (international format, digits only)',
        'instagram_url' => 'Instagram URL',
        'tiktok_url' => 'TikTok URL',
        'hero_status' => 'Hero status badge text (e.g. "open today · Pererenan")',
        'contact_email' => 'Contact e-mail',
        'nib_number' => 'NIB — business identification number (shown in legal pages & footer)',
        'registered_address' => 'Registered address (shown in legal pages & footer)',
        'legal_effective_date' => 'Legal pages — effective date text (e.g. "24 September 2026")',
        'hosting_provider' => 'Hosting provider + country (shown in the Privacy Policy)',
    ];

    /**
     * Payment settings — Midtrans can be switched on/off here.
     */
    public const PAYMENT = [
        'midtrans_enabled' => [
            'label' => 'Enable Midtrans payment links',
            'type' => 'toggle',
        ],
        'midtrans_environment' => [
            'label' => 'Environment',
            'type' => 'select',
            'options' => ['sandbox' => 'Sandbox (testing)', 'production' => 'Production (live)'],
        ],
        'midtrans_merchant_id' => [
            'label' => 'Merchant ID (MID)',
            'type' => 'text',
        ],
        'midtrans_client_key' => [
            'label' => 'Client key (SB-Mid-client-… / Mid-client-…)',
            'type' => 'text',
        ],
        'midtrans_server_key' => [
            'label' => 'Server key (leave as-is to keep the current one)',
            'type' => 'password',
        ],
        'bank_transfer_details' => [
            'label' => 'Bank transfer details (shown to admin as WhatsApp fallback)',
            'type' => 'textarea',
            'placeholder' => "Bank BCA\nAccount no: 1234567890\nAccount name: Kopi Rider",
        ],
    ];

    /**
     * Public legal pages — editable content (simple HTML allowed).
     */
    public const LEGAL = [
        'terms_conditions' => [
            'label' => 'Booking Terms & Conditions — page content',
            'type' => 'textarea',
            'rows' => 12,
        ],
        'terms_of_use' => [
            'label' => 'Website Terms of Use — page content',
            'type' => 'textarea',
            'rows' => 12,
        ],
        'privacy_policy' => [
            'label' => 'Privacy Policy — page content',
            'type' => 'textarea',
            'rows' => 12,
        ],
        'cookie_policy' => [
            'label' => 'Cookie Policy — page content',
            'type' => 'textarea',
            'rows' => 12,
        ],
    ];

    /**
     * SMTP e-mail settings — powers password resets & notifications.
     */
    public const SMTP = [
        'smtp_enabled' => [
            'label' => 'Enable SMTP e-mail (password reset, notifications)',
            'type' => 'toggle',
        ],
        'smtp_host' => [
            'label' => 'SMTP host',
            'type' => 'text',
            'placeholder' => 'smtp.gmail.com',
        ],
        'smtp_port' => [
            'label' => 'SMTP port',
            'type' => 'text',
            'placeholder' => '587',
        ],
        'smtp_encryption' => [
            'label' => 'Encryption',
            'type' => 'select',
            'options' => ['tls' => 'TLS (port 587 — most common)', 'ssl' => 'SSL (port 465)', 'null' => 'None (local dev only)'],
        ],
        'smtp_username' => [
            'label' => 'Username (usually the e-mail address)',
            'type' => 'text',
            'placeholder' => 'hello@kopirider.id',
        ],
        'smtp_password' => [
            'label' => 'Password / app password (leave as-is to keep the current one)',
            'type' => 'password',
        ],
        'smtp_from_address' => [
            'label' => 'From address',
            'type' => 'text',
            'placeholder' => 'hello@kopirider.id',
        ],
        'smtp_from_name' => [
            'label' => 'From name',
            'type' => 'text',
            'placeholder' => 'Kopi Rider',
        ],
    ];

    public function index()
    {
        $values = Setting::allCached();

        return view('admin.settings.index', [
            'general' => self::GENERAL,
            'payment' => self::PAYMENT,
            'legal' => self::LEGAL,
            'smtp' => self::SMTP,
            'values' => $values,
            'users' => \App\Models\User::orderBy('name')->get(),
            'midtransEnabled' => setting('midtrans_enabled') === '1',
            'smtpEnabled' => setting('smtp_enabled') === '1',
        ]);
    }

    /**
     * Change the signed-in admin's own password.
     */
    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => $data['password'], // auto-hashed by the model cast
        ]);

        return back()->with('success', 'Password updated successfully.');
    }

    /**
     * Upload a custom logo / favicon (stored on the public disk).
     */
    public function updateBranding(Request $request)
    {
        $data = $request->validate([
            'logo' => ['nullable', 'file', 'image', 'max:2048'],
            'favicon' => ['nullable', 'file', 'max:1024', 'mimes:png,jpg,jpeg,svg,webp,ico'],
        ]);

        foreach (['logo' => 'logo_path', 'favicon' => 'favicon_path'] as $field => $key) {
            if (! $request->hasFile($field) || ! $request->file($field)->isValid()) {
                continue;
            }

            // Remove the previous custom file (if any)
            if ($old = setting($key)) {
                Storage::disk('public')->delete($old);
            }

            Setting::set($key, $request->file($field)->store('branding', 'public'));
        }

        if (empty($data['logo']) && empty($data['favicon'])) {
            return back()->with('error', 'Choose a logo or favicon file first.');
        }

        return back()->with('success', 'Logo & favicon updated.');
    }

    /**
     * Send a branded test e-mail through the configured SMTP server.
     */
    public function sendTestEmail(Request $request)
    {
        $data = $request->validate([
            'test_email' => ['required', 'email'],
        ]);

        if (setting('smtp_enabled') !== '1') {
            return back()->with('error', 'Enable SMTP e-mail first, then save, then send a test.');
        }

        try {
            Mail::to($data['test_email'])->send(new TestMail($data['test_email']));
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'SMTP test failed: '.$e->getMessage());
        }

        return back()->with('success', 'Test e-mail sent to '.$data['test_email'].' — check the inbox (and spam folder).');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            ...collect(self::GENERAL)->mapWithKeys(fn ($l, $k) => [$k => ['nullable', 'string', 'max:300']])->all(),
            'midtrans_enabled' => ['nullable', 'boolean'],
            'midtrans_environment' => ['nullable', 'in:sandbox,production'],
            'midtrans_merchant_id' => ['nullable', 'string', 'max:100'],
            'midtrans_client_key' => ['nullable', 'string', 'max:100'],
            'midtrans_server_key' => ['nullable', 'string', 'max:100'],
            'bank_transfer_details' => ['nullable', 'string', 'max:1000'],
            'privacy_policy' => ['nullable', 'string', 'max:20000'],
            'terms_conditions' => ['nullable', 'string', 'max:20000'],
            'terms_of_use' => ['nullable', 'string', 'max:20000'],
            'cookie_policy' => ['nullable', 'string', 'max:20000'],
            'smtp_enabled' => ['nullable', 'boolean'],
            'smtp_host' => ['nullable', 'string', 'max:190'],
            'smtp_port' => ['nullable', 'integer', 'between:1,65535'],
            'smtp_encryption' => ['nullable', 'in:tls,ssl,null'],
            'smtp_username' => ['nullable', 'string', 'max:190'],
            'smtp_password' => ['nullable', 'string', 'max:190'],
            'smtp_from_address' => ['nullable', 'email', 'max:190'],
            'smtp_from_name' => ['nullable', 'string', 'max:100'],
        ]);

        foreach (array_keys(self::GENERAL) as $key) {
            Setting::set($key, $data[$key] ?? null);
        }

        // Toggle: on → '1', off → '0'
        Setting::set('midtrans_enabled', $request->boolean('midtrans_enabled') ? '1' : '0');
        Setting::set('midtrans_environment', $data['midtrans_environment'] ?? 'sandbox');
        Setting::set('midtrans_merchant_id', $data['midtrans_merchant_id'] ?? null);
        Setting::set('midtrans_client_key', $data['midtrans_client_key'] ?? null);

        // Never wipe an existing server key with an empty submit.
        if (! empty($data['midtrans_server_key'])) {
            Setting::set('midtrans_server_key', $data['midtrans_server_key']);
        }

        Setting::set('bank_transfer_details', $data['bank_transfer_details'] ?? null);

        // Public legal pages (empty = fall back to the built-in default text)
        Setting::set('privacy_policy', $data['privacy_policy'] ?? null);
        Setting::set('terms_conditions', $data['terms_conditions'] ?? null);
        Setting::set('terms_of_use', $data['terms_of_use'] ?? null);
        Setting::set('cookie_policy', $data['cookie_policy'] ?? null);

        /* ---------------- SMTP ---------------- */
        Setting::set('smtp_enabled', $request->boolean('smtp_enabled') ? '1' : '0');
        Setting::set('smtp_host', $data['smtp_host'] ?? null);
        Setting::set('smtp_port', (string) ($data['smtp_port'] ?? 587));
        Setting::set('smtp_encryption', $data['smtp_encryption'] ?? 'tls');
        Setting::set('smtp_username', $data['smtp_username'] ?? null);

        // Never wipe an existing SMTP password with an empty submit.
        if (! empty($data['smtp_password'])) {
            Setting::set('smtp_password', $data['smtp_password']);
        }

        Setting::set('smtp_from_address', $data['smtp_from_address'] ?? null);
        Setting::set('smtp_from_name', $data['smtp_from_name'] ?? null);

        // The mailer is configured at runtime from these settings —
        // flush the cached config so the next request picks them up.
        if (file_exists(base_path('bootstrap/cache/config.php'))) {
            Artisan::call('config:clear');
        }

        return back()->with('success', 'Settings saved.');
    }
}
