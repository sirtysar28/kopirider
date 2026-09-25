<?php

namespace App\Http\Controllers;

use App\Models\Package;

class PageController extends Controller
{
    public function checkDate()
    {
        return view('check-date', [
            'packages' => Package::active()->get(),
        ]);
    }

    public function packages()
    {
        return view('packages', [
            'packages' => Package::active()->get(),
        ]);
    }

    public function freeEvents()
    {
        return view('free-events');
    }

    public function gallery()
    {
        return view('gallery', [
            'items' => \App\Models\Media::active()->where('type', 'gallery')->get(),
        ]);
    }

    /**
     * Public legal pages — the content lives in the settings table
     * (editable from Admin → Settings → Legal pages) and falls back
     * to the built-in default copy in resources/views/legal/partials
     * when the setting is empty.
     */
    public function termsConditions()
    {
        return $this->legalPage('Booking Terms & Conditions', 'terms_conditions', 'legal.partials.terms-conditions');
    }

    public function termsOfUse()
    {
        return $this->legalPage('Website Terms of Use', 'terms_of_use', 'legal.partials.terms-of-use');
    }

    public function privacyPolicy()
    {
        return $this->legalPage('Privacy Policy', 'privacy_policy', 'legal.partials.privacy-policy');
    }

    public function cookiePolicy()
    {
        return $this->legalPage('Cookie Policy', 'cookie_policy', 'legal.partials.cookie-policy');
    }

    private function legalPage(string $title, string $settingKey, string $defaultView)
    {
        $content = setting($settingKey) ?: view($defaultView)->render();

        return view('legal', [
            'pageTitle' => $title,
            'content' => $this->withContactEmail($content),
        ]);
    }

    /** Swap the {email} placeholder for the contact e-mail setting. */
    private function withContactEmail(string $html): string
    {
        $email = setting('contact_email', 'admin@kopirider.com');

        return str_replace('{email}', e($email), $html);
    }
}

