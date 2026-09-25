{{-- Cookie consent banner + settings dialog (see js/cookie-consent.js) --}}
<div class="cookie-consent" id="cookieConsent" hidden aria-live="polite">
  <div class="cookie-consent-box">
    <p>
      We use essential cookies to make this site work. With your permission, we’d also like
      to use analytics and marketing cookies to understand how the site is used and to measure
      our ads. Read our <a href="{{ route('cookie-policy') }}">Cookie&nbsp;Policy</a>.
    </p>
    <div class="cookie-consent-actions">
      <button type="button" class="btn btn-sm" data-cookie-accept-all>Accept all</button>
      <button type="button" class="btn btn-sm btn-line" data-cookie-reject-nonessential>Reject non-essential</button>
      <button type="button" class="cookie-settings-link" data-cookie-settings>Settings</button>
    </div>
  </div>
</div>

<div class="cookie-settings-overlay" id="cookieSettingsOverlay" hidden role="dialog" aria-modal="true" aria-label="Cookie settings">
  <div class="cookie-settings">
    <button type="button" class="modal-close" data-cookie-close-settings aria-label="Close">✕</button>
    <h3>Cookie settings</h3>

    <label class="cookie-toggle-row">
      <span>
        <strong>Essential</strong> — Always on. Needed for the website to work and stay secure.
      </span>
      <input type="checkbox" checked disabled aria-label="Essential cookies (always on)">
    </label>

    <label class="cookie-toggle-row">
      <span>
        <strong>Analytics</strong> — Helps us understand how visitors use the website.
      </span>
      <input type="checkbox" id="cookieAnalytics" aria-label="Analytics cookies">
    </label>

    <label class="cookie-toggle-row">
      <span>
        <strong>Marketing</strong> — Measures our ads on social media and shows embedded content from Instagram and TikTok.
      </span>
      <input type="checkbox" id="cookieMarketing" aria-label="Marketing cookies">
    </label>

    <div class="cookie-consent-actions">
      <button type="button" class="btn btn-sm" data-cookie-save>Save choices</button>
      <button type="button" class="btn btn-sm btn-line" data-cookie-accept-all>Accept all</button>
    </div>
  </div>
</div>
