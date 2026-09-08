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
     * to the built-in default copy below when the setting is empty.
     */
    public function privacyPolicy()
    {
        return view('legal', [
            'pageTitle' => 'Privacy Policy',
            'content' => $this->withContactEmail(setting('privacy_policy') ?: self::DEFAULT_PRIVACY_POLICY),
        ]);
    }

    public function termsConditions()
    {
        return view('legal', [
            'pageTitle' => 'Terms & Conditions',
            'content' => $this->withContactEmail(setting('terms_conditions') ?: self::DEFAULT_TERMS_CONDITIONS),
        ]);
    }

    /** Swap the {email} placeholder for the contact e-mail setting. */
    private function withContactEmail(string $html): string
    {
        $email = setting('contact_email', 'hello@kopirider.id');

        return str_replace('{email}', e($email), $html);
    }

    private const DEFAULT_PRIVACY_POLICY = <<<'HTML'
<h3>Who we are</h3>
<p>Kopi Rider ("we", "us") is a coffee truck based in Bali, Indonesia. This policy explains what information we collect through this website and how we use it.</p>

<h3>What we collect</h3>
<ul>
  <li><b>Booking enquiries</b> — when you use the "Check your date" flow we ask for your name, WhatsApp number, e-mail (optional) and event details (date, venue, guest count, package).</li>
  <li><b>Usage data</b> — we record simple, anonymous analytics events (e.g. which package page is viewed) to improve the website.</li>
</ul>

<h3>How we use your information</h3>
<p>We use your details only to reply to your enquiry, prepare a quote, plan the event calendar and — if you explicitly ask for it — send you occasional updates. We do <b>not</b> sell or rent your personal data to anyone.</p>

<h3>Payments</h3>
<p>If you receive a payment link, deposits are processed by our payment provider (Midtrans). We only store the payment status and reference — never your card or bank credentials.</p>

<h3>Social media</h3>
<p>Our Instagram and TikTok pages are run by us. Interacting with them is subject to those platforms' own privacy policies.</p>

<h3>Your rights</h3>
<p>You can ask us at any time to show, correct or delete the personal data we hold about you. Just message us on WhatsApp or send an e-mail to <a href="mailto:{email}">{email}</a>.</p>

<h3>Changes to this policy</h3>
<p>If we update this policy we will publish the new version on this page.</p>
HTML;

    private const DEFAULT_TERMS_CONDITIONS = <<<'HTML'
<h3>Our services</h3>
<p>Kopi Rider provides mobile coffee truck services in Bali for weddings, private parties, markets and other events. All services are subject to availability and confirmation by our team.</p>

<h3>Quotes &amp; pricing</h3>
<p>Prices shown on this website are starting prices. The final quote is confirmed personally via WhatsApp and depends on the date, location, guest count and menu chosen.</p>

<h3>Booking &amp; deposit</h3>
<p>A booking is only confirmed after we have explicitly confirmed your date and received the agreed deposit. Dates without a confirmed booking remain available to other customers.</p>

<h3>Cancellation</h3>
<p>Cancellation terms are agreed in writing (WhatsApp or e-mail) at the moment of booking. Deposits may be non-refundable when a cancellation occurs within 14 days of the event date.</p>

<h3>Free events</h3>
<p>For free events we serve coffee on a first-come, first-served basis while supplies last, unless a private arrangement has been made.</p>

<h3>Liability</h3>
<p>We carry public liability insurance for our operations, but we are not responsible for loss or damage caused by circumstances outside our control, or by guests themselves.</p>

<h3>Changes</h3>
<p>We may update these terms from time to time. The version published on this page is the version that applies.</p>

<h3>Contact</h3>
<p>Questions about these terms? Message us on WhatsApp or e-mail <a href="mailto:{email}">{email}</a>.</p>
HTML;
}
