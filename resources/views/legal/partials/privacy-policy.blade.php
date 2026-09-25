@php($effectiveDate = setting('legal_effective_date', '24 September 2026'))
@php($nib = setting('nib_number'))
@php($address = setting('registered_address'))
@php($email = setting('contact_email', 'admin@kopirider.com'))
@php($hosting = setting('hosting_provider'))

<p class="legal-meta">Version 1.0 · Effective {{ $effectiveDate }}</p>

<p>
  This policy explains how Kopi Rider collects and uses your personal data, in line with Indonesia’s
  Personal Data Protection Law (Law No. 27 of 2022) and, where it applies, the EU General Data
  Protection Regulation (GDPR).
</p>

<h3>1. Who we are</h3>
<p>
  Kopi Rider is responsible for your personal data (the “data controller”).
  @if ($nib)
    Kopi Rider is registered in Indonesia under NIB {{ $nib }}.
  @else
    Kopi Rider is registered in Indonesia (business identification number available on request).
  @endif
  @if ($address)
    Registered address: {{ $address }}.
  @endif
  Privacy contact: <a href="mailto:{{ $email }}">{{ $email }}</a>.
</p>

<h3>2. What we collect</h3>
<p><strong>2.1 Booking requests:</strong> your name and WhatsApp number, and the details of your event — date, type of event, number of guests, package and location.</p>
<p><strong>2.2 Messages:</strong> what you send us on WhatsApp, Instagram or TikTok, including conversations with our WhatsApp booking assistant.</p>
<p><strong>2.3 Bookings and payments:</strong> your quote, invoices and payment status. Online payments are processed by Midtrans; we do not receive or store your full card details. When you pay at the truck, the payment goes through our point-of-sale system (Moka).</p>
<p><strong>2.4 Allergies and dietary needs:</strong> only if you tell us, so we can serve you safely.</p>
<p><strong>2.5 Photos and video:</strong> images from events and around the truck (see section 7).</p>
<p><strong>2.6 Website use:</strong> technical data such as your device, browser and IP address, and — only if you accept them — analytics and marketing cookies (see our <a href="{{ route('cookie-policy') }}">Cookie Policy</a>).</p>

<h3>3. Why we use your data, and our legal basis</h3>
<ul>
  <li>To answer your request and prepare a quote — steps you ask us to take before a contract.</li>
  <li>To manage your booking and take payment — performance of our contract with you.</li>
  <li>To handle allergy and dietary information — your explicit consent, which you give when you share it with us.</li>
  <li>To send you news and offers — your consent, which you can withdraw at any time.</li>
  <li>To publish photos or video in which you can be recognised — your written consent.</li>
  <li>To keep accounting and tax records — our legal obligations.</li>
  <li>To keep our website secure and improve our service — our legitimate interests, balanced against your rights.</li>
</ul>

<h3>4. Who we share your data with</h3>
<p>We share only what is needed, with:</p>
<ul>
  <li>Midtrans — to process online payments;</li>
  <li>Moka — our point-of-sale system, for payments at the truck;</li>
  <li>Meta (WhatsApp and Instagram) — when you contact us there, and to run our WhatsApp booking assistant;</li>
  <li>TikTok — when you contact us there;</li>
  <li>{{ $hosting ?: 'our hosting provider' }} — {{ $hosting ? 'which' : 'who' }} hosts our website;</li>
  <li>our website developer — to maintain the website;</li>
  <li>partner restaurants and bakeries — only your name, event date and dietary needs, when they prepare food for your booking;</li>
  <li>authorities — when the law requires it.</li>
</ul>
<p>We never sell your personal data.</p>

<h3>5. Transfers outside Indonesia</h3>
<p>Some of these providers store or process data outside Indonesia. When we transfer your data abroad, we do so as Indonesian law requires — for example to countries or recipients with an equal or higher level of protection, under appropriate safeguards such as contract clauses, or with your consent.</p>

<h3>6. Our WhatsApp booking assistant</h3>
<p>The first part of a WhatsApp conversation may be handled by an automated assistant that checks our available dates and answers booking questions. It is a booking assistant only, and you can ask for a person at any time. It does not make decisions with legal or similarly significant effects for you — a member of our team confirms every booking.</p>

<h3>7. Photos and video</h3>
<p>We only publish photos or video in which you can be recognised if you have given written consent — for example by ticking the photo box on our booking form or signing a consent form at the event. You can withdraw consent for future use at any time. We will then stop using the images, but we may not be able to remove copies that others have already shared.</p>

<h3>8. How long we keep your data</h3>
<ul>
  <li>Requests that do not become bookings: kept for up to 12 months, then deleted. A request only becomes a booking when the 50% deposit is paid.</li>
  <li>Booking and payment records: as long as Indonesian tax and accounting law requires.</li>
  <li>Allergy and dietary requests: kept with your booking records.</li>
  <li>News and offers: until you unsubscribe.</li>
  <li>Photos and video with consent: until you withdraw consent.</li>
  <li>WhatsApp conversations: for as long as we use WhatsApp for our business.</li>
</ul>

<h3>9. Your rights</h3>
<p>You have the right to: know how your data is used; get a copy of it; have it corrected or completed; have it deleted; withdraw your consent; object to or limit how we use it; receive it in a commonly used format; and complain. To use these rights, email <a href="mailto:{{ $email }}">{{ $email }}</a> or use the WhatsApp button on our website. We reply within the time limits set by Indonesian law — for requests to access your data, within 3 × 24 hours. Withdrawing consent does not affect what we did before you withdrew it.</p>

<h3>10. Security</h3>
<p>We protect your data with reasonable technical and organisational measures, including secure (HTTPS) connections, access controls and trusted providers.</p>

<h3>11. If something goes wrong</h3>
<p>If a data breach affects your personal data, we will inform you and the relevant authority in writing within 3 × 24 hours, as Indonesian law requires.</p>

<h3>12. Children</h3>
<p>Bookings are made by adults. We do not knowingly collect personal data from children without the involvement of a parent or guardian.</p>

<h3>13. Changes to this policy</h3>
<p>We may update this policy. The current version is always on this page, with its effective date.</p>

<h3>14. Contact and complaints</h3>
<p>Questions or complaints: <a href="mailto:{{ $email }}">{{ $email }}</a>. You can also contact the Indonesian authority responsible for personal data protection. If you live in the European Union, you can also complain to your local data protection authority.</p>
