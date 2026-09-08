<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

if (! function_exists('setting')) {
    /**
     * Get a setting value by key with optional default.
     */
    function setting(string $key, ?string $default = null): ?string
    {
        $all = Setting::allCached();

        return $all[$key] ?? $default;
    }
}

if (! function_exists('wa_link')) {
    /**
     * Build a WhatsApp click-to-chat URL.
     */
    function wa_link(?string $number = null, ?string $message = null): string
    {
        $number = preg_replace('/\D+/', '', $number ?? setting('whatsapp_number', '6281234567890'));

        $url = 'https://wa.me/'.$number;

        return $message ? $url.'?text='.rawurlencode($message) : $url;
    }
}

if (! function_exists('logo_url')) {
    /**
     * Site logo — custom upload from settings, else the bundled default.
     * asset('storage/…') keeps the URL relative to the current host.
     */
    function logo_url(): string
    {
        $path = setting('logo_path');

        return $path ? asset('storage/'.$path) : asset('img/logo-icon.svg');
    }
}

if (! function_exists('favicon_url')) {
    /**
     * Favicon — custom upload from settings, else the bundled default.
     */
    function favicon_url(): string
    {
        $path = setting('favicon_path');

        return $path ? asset('storage/'.$path) : asset('img/favicon-96.png');
    }
}

if (! function_exists('has_custom_favicon')) {
    function has_custom_favicon(): bool
    {
        return (bool) setting('favicon_path');
    }
}
