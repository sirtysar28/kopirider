@php($effectiveDate = setting('legal_effective_date', '24 September 2026'))
@php($email = setting('contact_email', 'admin@kopirider.com'))

<p class="legal-meta">Version 1.0 · Effective {{ $effectiveDate }}</p>

<h3>1. What cookies are</h3>
<p>Cookies are small files that a website stores on your device. Some are needed for the site to work. Others help us understand how the site is used or measure our ads.</p>

<h3>2. The cookies we use</h3>
<p><strong>Essential cookies (always on):</strong> keep the website working and secure — for example, remembering your answers while you check a date, and protecting our forms.</p>
<p><strong>Analytics cookies (only if you accept):</strong> show us how visitors use the website, so we can improve it.</p>
<p><strong>Marketing cookies (only if you accept):</strong> let us measure our ads on social media and show embedded content from services such as Instagram and TikTok.</p>

<h3>3. Your choices</h3>
<p>On your first visit you can accept all cookies, reject the non-essential ones, or choose by category. You can change your choice at any time via “Cookie settings” at the bottom of every page. You can also delete cookies in your browser settings.</p>

<h3>4. Cookie list</h3>
<div class="table-wrap">
  <table class="cookie-table">
    <thead>
      <tr>
        <th>Cookie</th>
        <th>Set by</th>
        <th>Purpose</th>
        <th>Type</th>
        <th>Expires</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><code>kopi_rider_session</code></td>
        <td>Kopi Rider (this website)</td>
        <td>Keeps your session working while you use the website.</td>
        <td>Essential</td>
        <td>Session (deleted when you close your browser)</td>
      </tr>
      <tr>
        <td><code>XSRF-TOKEN</code></td>
        <td>Kopi Rider (this website)</td>
        <td>Protects our forms against cross-site request forgery.</td>
        <td>Essential</td>
        <td>Session (deleted when you close your browser)</td>
      </tr>
      <tr>
        <td><code>kr_cookie_consent</code></td>
        <td>Kopi Rider (this website)</td>
        <td>Remembers your cookie choices so we do not ask again.</td>
        <td>Essential</td>
        <td>6 months</td>
      </tr>
      <tr>
        <td><code>remember_web_…</code></td>
        <td>Kopi Rider (this website)</td>
        <td>Keeps an admin user signed in when they tick “remember me”. Only set for team members on the admin login page.</td>
        <td>Essential</td>
        <td>Up to 5 years (only for admin “remember me”)</td>
      </tr>
    </tbody>
  </table>
</div>
<p>Our website analytics are first-party: they are recorded by our own server, without third-party tracking cookies. If we add third-party analytics or marketing tools later, we will update this list and ask for your consent first.</p>

<h3>5. More information</h3>
<p>See our <a href="{{ route('privacy-policy') }}">Privacy Policy</a> or contact us at <a href="mailto:{{ $email }}">{{ $email }}</a>.</p>
