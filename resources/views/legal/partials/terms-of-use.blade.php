@php($effectiveDate = setting('legal_effective_date', '24 September 2026'))
@php($nib = setting('nib_number'))
@php($address = setting('registered_address'))
@php($email = setting('contact_email', 'admin@kopirider.com'))

<p class="legal-meta">Version 1.0 · Effective {{ $effectiveDate }}</p>

<h3>1. About us</h3>
<p>
  This website, kopirider.com, is operated by Kopi Rider,
  @if ($nib)
    registered in Indonesia under NIB {{ $nib }}.
  @else
    registered in Indonesia (business identification number available on request).
  @endif
  @if ($address)
    Registered address: {{ $address }}.
  @endif
  In these terms, “Kopi Rider”, “we” and “us” mean Kopi Rider.
  Contact: <a href="mailto:{{ $email }}">{{ $email }}</a>, or use the WhatsApp button on our website.
</p>

<h3>2. What this website does</h3>
<p>The website shows our coffee truck services and starting prices, and lets you check dates and send booking requests. Bookings are governed by our <a href="{{ route('terms-and-conditions') }}">Booking Terms &amp; Conditions</a>.</p>

<h3>3. Using this website</h3>
<p>By using this website you agree to these terms. If you do not agree, please do not use it.</p>

<h3>4. Age</h3>
<p>You must be at least 18 years old to send a booking request.</p>

<h3>5. Prices and information</h3>
<p>All prices are in Indonesian Rupiah (IDR). Prices shown are starting prices; your final price is confirmed in writing. We work hard to keep information accurate, but it may contain errors, and we may change content, prices and availability until a booking is confirmed.</p>

<h3>6. Availability</h3>
<p>The availability calendar is a guide only. A date is reserved only when your booking is confirmed under our Booking Terms &amp; Conditions.</p>

<h3>7. Other websites and services</h3>
<p>This website links to, and may show content from, other services such as Instagram, TikTok, WhatsApp, Google Maps and our payment provider Midtrans. Their own terms and privacy policies apply, and we are not responsible for their content.</p>

<h3>8. Our content</h3>
<p>The Kopi Rider name, logo, photos, texts and design belong to Kopi Rider or are used with permission. Please do not copy or use them without our written permission.</p>

<h3>9. Fair use</h3>
<p>Please do not misuse the website — for example by sending false requests or spam, trying to access data or parts of the site you are not allowed to access, or disrupting the site. If you send false requests or spam, we will block you from using our services in the future.</p>

<h3>10. Our responsibility</h3>
<p>We provide the website “as is”. As far as Indonesian law allows, we are not responsible for loss caused by using the website itself. Nothing in these terms limits any liability that cannot be limited under Indonesian law, including Law No. 8 of 1999 on Consumer Protection, and nothing here affects your rights under our Booking Terms &amp; Conditions.</p>

<h3>11. Privacy</h3>
<p>Our <a href="{{ route('privacy-policy') }}">Privacy Policy</a> explains how we use your personal data.</p>

<h3>12. Changes</h3>
<p>We may update these terms. The version on this page applies when you use the website.</p>

<h3>13. Law and disputes</h3>
<p>These terms are governed by the laws of the Republic of Indonesia. We will first try to solve any dispute together. If we cannot, you may take it to the Consumer Dispute Settlement Body (BPSK) or to the competent court in Indonesia.</p>
