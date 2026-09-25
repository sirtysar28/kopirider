/* ============================================================
   KOPI RIDER — Cookie consent (banner + settings dialog)
   Choice is stored in the kr_cookie_consent cookie (6 months).
   ============================================================ */
(function () {
  'use strict';

  var COOKIE_NAME = 'kr_cookie_consent';
  var MAX_AGE = 60 * 60 * 24 * 182; // ~6 months

  function readConsent() {
    try {
      var m = document.cookie.match(new RegExp('(?:^|;\\s*)' + COOKIE_NAME + '=([^;]*)'));
      return m ? JSON.parse(decodeURIComponent(m[1])) : null;
    } catch (e) { return null; }
  }

  function writeConsent(data) {
    var payload = { essential: true, analytics: !!data.analytics, marketing: !!data.marketing, ts: Date.now() };
    document.cookie = COOKIE_NAME + '=' + encodeURIComponent(JSON.stringify(payload)) +
      '; path=/; max-age=' + MAX_AGE + '; SameSite=Lax';
    applyConsent(payload);
  }

  /**
   * Hook for future third-party tags: they should check
   * window.krCookieConsent.analytics / .marketing before loading.
   */
  function applyConsent(payload) {
    window.krCookieConsent = payload;
    document.dispatchEvent(new CustomEvent('kr:cookie-consent', { detail: payload }));
  }

  function hideBanner() {
    var b = document.getElementById('cookieConsent');
    if (b) b.hidden = true;
  }

  function showBanner() {
    var b = document.getElementById('cookieConsent');
    if (b) b.hidden = false;
  }

  function openSettings() {
    var o = document.getElementById('cookieSettingsOverlay');
    if (!o) return;
    var c = readConsent() || window.krCookieConsent || {};
    var a = document.getElementById('cookieAnalytics');
    var m = document.getElementById('cookieMarketing');
    if (a) a.checked = !!c.analytics;
    if (m) m.checked = !!c.marketing;
    o.hidden = false;
  }

  function closeSettings() {
    var o = document.getElementById('cookieSettingsOverlay');
    if (o) o.hidden = true;
  }

  function init() {
    var consent = readConsent();
    if (consent) {
      applyConsent(consent);
    } else {
      window.krCookieConsent = { essential: true, analytics: false, marketing: false };
      // Small delay so the page renders first
      setTimeout(showBanner, 600);
    }

    document.addEventListener('click', function (e) {
      var settingsBtn = e.target.closest('[data-cookie-settings]');
      if (settingsBtn) e.preventDefault();
      if (e.target.closest('[data-cookie-accept-all]')) {
        writeConsent({ analytics: true, marketing: true });
        hideBanner(); closeSettings();
      } else if (e.target.closest('[data-cookie-reject-nonessential]')) {
        writeConsent({ analytics: false, marketing: false });
        hideBanner();
      } else if (e.target.closest('[data-cookie-settings]')) {
        openSettings();
      } else if (e.target.closest('[data-cookie-save]')) {
        var a = document.getElementById('cookieAnalytics');
        var m = document.getElementById('cookieMarketing');
        writeConsent({ analytics: !!(a && a.checked), marketing: !!(m && m.checked) });
        hideBanner(); closeSettings();
      } else if (e.target.closest('[data-cookie-close-settings]')) {
        closeSettings();
      }
    });

    var overlay = document.getElementById('cookieSettingsOverlay');
    if (overlay) {
      overlay.addEventListener('click', function (e) {
        if (e.target === overlay) closeSettings();
      });
    }

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closeSettings();
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
