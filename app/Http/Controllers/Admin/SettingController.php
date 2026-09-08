<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
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
        'privacy_policy' => [
            'label' => 'Privacy Policy — page content',
            'type' => 'textarea',
            'rows' => 12,
        ],
        'terms_conditions' => [
            'label' => 'Terms & Conditions — page content',
            'type' => 'textarea',
            'rows' => 12,
        ],
    ];

    public function index()
    {
        $values = Setting::allCached();

        return view('admin.settings.index', [
            'general' => self::GENERAL,
            'payment' => self::PAYMENT,
            'legal' => self::LEGAL,
            'values' => $values,
            'users' => \App\Models\User::orderBy('name')->get(),
            'midtransEnabled' => setting('midtrans_enabled') === '1',
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

        return back()->with('success', 'Settings saved.');
    }
}
