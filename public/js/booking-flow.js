/* ============================================================
   KOPI RIDER — Booking Flow
   4 steps · calendar from API · partial leads saved after every
   step · lead committed to DB BEFORE the WhatsApp redirect.
   ============================================================ */
(function () {
  'use strict';

  // ---------- session ----------
  const SID_KEY = 'kr_session';
  let sessionId = null;
  try {
    sessionId = localStorage.getItem(SID_KEY);
    if (!sessionId) {
      sessionId = 's_' + Date.now().toString(36) + Math.random().toString(36).slice(2, 10);
      localStorage.setItem(SID_KEY, sessionId);
    }
  } catch (e) {
    sessionId = 's_' + Date.now().toString(36);
  }

  const state = {
    step: 1,
    date: null,
    eventType: null,
    guestRange: null,
    packageId: null,
    calendarMonth: null, // 'YYYY-MM' being displayed
    availability: {},
    savedDate: null,
    savedEvent: null,
    savedGuests: null,
    savedPackage: null,
  };

  const FREE_EVENTS = ['market', 'festival', 'community'];
  const EVENT_LABELS = {
    wedding: 'wedding', private: 'private party', corporate: 'corporate event',
    market: 'market', festival: 'festival', community: 'community event', other: 'event',
  };

  // ---------- helpers ----------
  const $ = (sel, ctx) => (ctx || document).querySelector(sel);
  const $$ = (sel, ctx) => Array.from((ctx || document).querySelectorAll(sel));

  function track(event, meta) {
    if (window.gtag) gtag('event', event, { event_category: 'booking', ...(meta || {}) });
    fetch('/api/analytics', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify({ event_name: event, session_id: sessionId, meta: meta || {} }),
    }).catch(function () {});
  }

  function toast(msg, ms) {
    let t = $('#krToast');
    if (!t) {
      t = document.createElement('div');
      t.className = 'toast';
      t.id = 'krToast';
      document.body.appendChild(t);
    }
    t.textContent = msg;
    requestAnimationFrame(function () { t.classList.add('show'); });
    clearTimeout(t._timer);
    t._timer = setTimeout(function () { t.classList.remove('show'); }, ms || 3200);
  }

  // ---------- calendar ----------
  function loadCalendar(month) {
    const grid = $('#calendarGrid');
    if (grid) grid.innerHTML = '<div class="day-label" style="grid-column:1/-1;text-align:center;padding:20px;color:#A6906F;">Loading calendar…</div>';

    fetch('/api/calendar?month=' + encodeURIComponent(month), { headers: { 'Accept': 'application/json' } })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        state.availability = data.days || {};
        state.calendarMonth = month;
        renderCalendar();
      })
      .catch(function () {
        if (grid) grid.innerHTML = '<div class="day-label" style="grid-column:1/-1;text-align:center;padding:20px;color:#8B4226;">Could not load the calendar. Please try again.</div>';
      });
  }

  function statusFor(dateStr) {
    return state.availability[dateStr] || 'available';
  }

  function renderCalendar() {
    const grid = $('#calendarGrid');
    if (!grid) return;

    const month = state.calendarMonth;
    const year = parseInt(month.slice(0, 4), 10);
    const mon = parseInt(month.slice(5, 7), 10); // 1-12
    const firstDay = new Date(year, mon - 1, 1);
    const daysInMonth = new Date(year, mon, 0).getDate();
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    $('#calTitle').textContent = firstDay.toLocaleDateString('en-GB', { month: 'long', year: 'numeric' });

    let html = '';
    ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'].forEach(function (d) { html += '<div class="day-label">' + d + '</div>'; });

    const offset = (firstDay.getDay() + 6) % 7; // Monday-first
    for (let i = 0; i < offset; i++) html += '<div class="date-cell empty"></div>';

    for (let d = 1; d <= daysInMonth; d++) {
      const mm = String(mon).padStart(2, '0');
      const dd = String(d).padStart(2, '0');
      const dateStr = year + '-' + mm + '-' + dd;
      const cell = new Date(year, mon - 1, d);
      const isPast = cell < today;
      const isToday = cell.getTime() === today.getTime();
      const st = statusFor(dateStr);
      const cls = st === 'available' ? 'green' : st === 'enquiry' ? 'yellow' : 'red';
      const selected = state.date === dateStr ? ' selected' : '';
      html += '<div class="date-cell ' + cls + (isPast ? ' past' : '') + (isToday ? ' today' : '') + selected +
        '" data-date="' + dateStr + '" role="button" tabindex="0" aria-label="' + dateStr + '">' + d + '</div>';
    }
    grid.innerHTML = html;

    $$('.date-cell:not(.past):not(.red):not(.empty)', grid).forEach(function (cell) {
      cell.addEventListener('click', function () {
        selectDate(cell.dataset.date);
      });
      cell.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); selectDate(cell.dataset.date); }
      });
    });
  }

  function selectDate(dateStr) {
    state.date = dateStr;
    $$('.date-cell').forEach(function (c) { c.classList.remove('selected'); });
    const cell = $('.date-cell[data-date="' + dateStr + '"]');
    if (cell) cell.classList.add('selected');

    const pretty = new Date(dateStr + 'T00:00:00').toLocaleDateString('en-GB', {
      weekday: 'long', day: 'numeric', month: 'long', year: 'numeric',
    });
    $('#selectedDateDisplay').textContent = '📅 ' + pretty;

    const hint = $('#calHint');
    const st = statusFor(dateStr);
    hint.innerHTML = st === 'enquiry'
      ? '⚠️ Someone has already asked about this date — send your request and we\'ll confirm who gets it.'
      : '✅ This date looks available. A member of our team will confirm it with you.';
    hint.classList.add('show');

    $('#step1Next').disabled = false;
    track('date_selected', { date: dateStr, status: st });
    savePartial();
  }

  // ---------- step rendering ----------
  function goStep(step) {
    state.step = step;
    $$('.flow-step').forEach(function (s) { s.classList.remove('active'); });
    const el = $('.flow-step[data-step="' + step + '"]');
    if (el) el.classList.add('active');

    $$('.progress-step').forEach(function (p, i) {
      p.classList.remove('active', 'done');
      const n = i + 1;
      if (n === step) p.classList.add('active');
      else if (n < step) p.classList.add('done');
    });

    if (step === 1) {
      $('#step1Next').disabled = !state.date;
    }
    if (step === 2) {
      $('#step2Next').disabled = !(state.eventType && state.guestRange);
    }
    if (step === 3) renderStep3();
    if (step === 4) { track('contact_screen_viewed'); updatePreview(); }

    const modal = $('#bookingModal');
    if (modal && modal.classList.contains('open')) {
      $('.modal', modal).scrollTop = 0;
    }
  }

  function renderStep3() {
    const wrapEl = $('#step3Content');
    const title = $('#step3Title');
    if (!wrapEl) return;
    track('package_viewed', { event_type: state.eventType });

    if (FREE_EVENTS.indexOf(state.eventType) !== -1) {
      title.textContent = '🎪 Free event option';
      wrapEl.innerHTML =
        '<div class="free-info" style="margin:0;">' +
        '<p style="font-size:16px;"><strong>You pay nothing upfront.</strong> We come to your event, set up the truck, and make our money selling coffee to your visitors.</p>' +
        '<ul class="check-list">' +
        '<li>✔ <span>No cost to you or the organiser upfront</span></li>' +
        '<li>✔ <span>We agree a <span class="highlight">minimum sales guarantee</span> beforehand</span></li>' +
        '<li>✔ <span>If coffee sales fall short, the organiser covers the difference</span></li>' +
        '<li>✔ <span>We handle everything — coffee, baristas, power, setup</span></li>' +
        '</ul>' +
        '<p style="font-size:13.5px;color:#7C6650;">The exact minimum depends on your event — we\'ll discuss it on WhatsApp before anything is agreed.</p>' +
        '</div>';
      state.packageId = null;
      $('#step3Next').disabled = false;
      return;
    }

    title.textContent = '💼 Choose your package';
    const packages = window.KR_PACKAGES || [];
    let html = '<div class="pkg-grid" style="grid-template-columns:1fr 1fr;">';
    packages.forEach(function (p) {
      html +=
        '<div class="pkg-card" data-pkg="' + p.id + '">' +
        '<h3>' + p.name + '</h3>' +
        '<div class="pkg-price">From ' + p.price + '<small>Final pricing confirmed by our team</small></div>' +
        '<ul>' + p.features.map(function (f) { return '<li>✅ ' + f + '</li>'; }).join('') + '</ul>' +
        '<button class="btn btn-sm btn-block selectPkg" data-pkg="' + p.id + '">Select</button>' +
        '</div>';
    });
    html += '</div><p style="font-size:13.5px;color:#7C6650;margin-top:6px;">Need something bigger or different? Custom quotes available — just ask on WhatsApp.</p>';
    wrapEl.innerHTML = html;

    $$('.selectPkg', wrapEl).forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.stopPropagation();
        state.packageId = parseInt(btn.dataset.pkg, 10);
        $$('.selectPkg', wrapEl).forEach(function (b) {
          b.textContent = 'Select';
          b.classList.remove('btn-sage');
        });
        btn.textContent = '✓ Selected';
        btn.style.background = '#5B6B45';
        btn.style.borderColor = '#5B6B45';
        track('package_selected', { package_id: state.packageId });
        $('#step3Next').disabled = false;
        savePartial();
      });
    });
    $('#step3Next').disabled = !state.packageId;
  }

  function updatePreview() {
    const pretty = state.date
      ? new Date(state.date + 'T00:00:00').toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' })
      : '[date]';
    const evt = state.eventType ? EVENT_LABELS[state.eventType] : '[event type]';
    const guests = state.guestRange || '[guests]';
    const el = $('#previewMessage');
    if (el) el.textContent = "Hi! I'd like to book the truck for " + pretty + ", a " + evt + ", around " + guests + " guests.";
  }

  // ---------- partial lead persistence ----------
  let partialTimer = null;
  function savePartial() {
    clearTimeout(partialTimer);
    partialTimer = setTimeout(function () {
      if (state.savedDate === state.date && state.savedEvent === state.eventType &&
        state.savedGuests === state.guestRange && state.savedPackage === state.packageId) return;
      if (!state.date && !state.eventType && !state.guestRange) return;

      const payload = { session_id: sessionId, complete: false };
      if (state.date) payload.event_date = state.date;
      if (state.eventType) payload.event_type = state.eventType;
      if (state.guestRange) payload.guest_range = state.guestRange;
      if (state.packageId) payload.package_id = state.packageId;

      fetch('/api/leads', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(payload),
        keepalive: true,
      }).then(function () {
        state.savedDate = state.date;
        state.savedEvent = state.eventType;
        state.savedGuests = state.guestRange;
        state.savedPackage = state.packageId;
      }).catch(function () {});
    }, 600);
  }

  // ---------- submit: DB first, WhatsApp second ----------
  function submitLead() {
    const name = $('#leadName').value.trim();
    const wa = $('#leadWhatsApp').value.trim();

    if (!name || !wa) {
      toast('Please fill in your name and WhatsApp number.');
      return;
    }
    if (!/^\+?[0-9\s\-()]{8,20}$/.test(wa)) {
      toast('That WhatsApp number doesn\'t look right — include the country code.');
      return;
    }

    const btn = $('#submitLead');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span>&nbsp; Saving your request…';

    fetch('/api/leads', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify({
        session_id: sessionId,
        event_date: state.date,
        event_type: state.eventType,
        guest_range: state.guestRange,
        package_id: state.packageId,
        name: name,
        whatsapp: wa,
        complete: true,
      }),
    })
      .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
      .then(function (res) {
        if (!res.ok || !res.data.saved) {
          throw new Error(res.data.message || 'Could not save your request.');
        }

        // Lead is committed — show success + reference
        track('lead_submitted', { reference: res.data.reference });
        goStep(5);
        $('#leadRef').textContent = res.data.reference;

        const waUrl = res.data.whatsapp_url;
        // Fire-and-forget: WhatsApp opens in a new tab, keepalive keeps the beacon alive
        fetch('/api/leads/whatsapp-opened', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ session_id: sessionId }),
          keepalive: true,
        }).catch(function () {});

        btn.disabled = false;
        btn.innerHTML = 'Send enquiry';

        if (waUrl) {
          track('whatsapp_opened', { reference: res.data.reference });
          // Small delay so the success screen is actually seen
          setTimeout(function () { window.open(waUrl, '_blank'); }, 900);
        }
      })
      .catch(function (err) {
        toast(err.message || 'Something went wrong — please try again.', 4200);
        btn.disabled = false;
        btn.innerHTML = 'Send enquiry';
      });
  }

  // ---------- modal open/close ----------
  function openFlow(opts) {
    const overlay = $('#bookingModal');
    if (!overlay) { window.location.href = '/check-date'; return; }
    overlay.classList.add('open');
    document.body.style.overflow = 'hidden';

    if (!state.calendarMonth) {
      const now = new Date();
      loadCalendar(now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0'));
    } else {
      renderCalendar();
    }
    track('booking_flow_opened');

    if (opts && opts.eventType) {
      state.eventType = opts.eventType;
      $$('#eventTypeGroup button').forEach(function (b) {
        b.classList.toggle('selected', b.dataset.value === opts.eventType);
      });
    }
    goStep(1);
  }

  function closeFlow() {
    const overlay = $('#bookingModal');
    if (overlay) overlay.classList.remove('open');
    document.body.style.overflow = '';
    savePartial();
  }

  // ---------- wire up ----------
  document.addEventListener('DOMContentLoaded', function () {
    // openers
    document.addEventListener('click', function (e) {
      const opener = e.target.closest('[data-open-booking]');
      if (opener) {
        e.preventDefault();
        const preset = opener.getAttribute('data-event-type');
        openFlow(preset ? { eventType: preset } : {});
        return;
      }
      if (e.target.closest('[data-close-booking]')) { e.preventDefault(); closeFlow(); }
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closeFlow();
    });
    const overlay = $('#bookingModal');
    if (overlay) {
      overlay.addEventListener('click', function (e) { if (e.target === overlay) closeFlow(); });
    }

    // calendar nav
    const prevBtn = $('#calPrev'), nextBtn = $('#calNext');
    function shiftMonth(delta) {
      const m = state.calendarMonth || (new Date().getFullYear() + '-' + String(new Date().getMonth() + 1).padStart(2, '0'));
      const d = new Date(parseInt(m.slice(0, 4), 10), parseInt(m.slice(5, 7), 10, 10) - 1 + delta, 1);
      const now = new Date();
      if (d < new Date(now.getFullYear(), now.getMonth(), 1)) return;
      loadCalendar(d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0'));
    }
    if (prevBtn) prevBtn.addEventListener('click', function () { shiftMonth(-1); });
    if (nextBtn) nextBtn.addEventListener('click', function () { shiftMonth(1); });

    // step navigation
    const s1 = $('#step1Next');
    if (s1) s1.addEventListener('click', function () { if (state.date) goStep(2); });

    $$('#eventTypeGroup button').forEach(function (btn) {
      btn.addEventListener('click', function () {
        $$('#eventTypeGroup button').forEach(function (b) { b.classList.remove('selected'); });
        btn.classList.add('selected');
        state.eventType = btn.dataset.value;
        state.packageId = null; // package depends on event type
        $('#step2Next').disabled = !(state.eventType && state.guestRange);
        track('event_type_selected', { event_type: state.eventType });
        updatePreview();
        savePartial();
      });
    });

    $$('#guestRangeGroup button').forEach(function (btn) {
      btn.addEventListener('click', function () {
        $$('#guestRangeGroup button').forEach(function (b) { b.classList.remove('selected'); });
        btn.classList.add('selected');
        state.guestRange = btn.dataset.value;
        $('#step2Next').disabled = !(state.eventType && state.guestRange);
        track('guest_range_selected', { guest_range: state.guestRange });
        updatePreview();
        savePartial();
      });
    });

    const s2 = $('#step2Next');
    if (s2) s2.addEventListener('click', function () { if (state.eventType && state.guestRange) goStep(3); });

    const s3 = $('#step3Next');
    if (s3) s3.addEventListener('click', function () {
      const isFree = FREE_EVENTS.indexOf(state.eventType) !== -1;
      if (isFree || state.packageId) goStep(4);
      else toast('Please select a package first.');
    });

    $$('[data-prev]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        if (state.step > 1) goStep(state.step - 1);
      });
    });

    const submit = $('#submitLead');
    if (submit) submit.addEventListener('click', submitLead);

    // initial render
    if ($('#calendarGrid')) {
      const now = new Date();
      loadCalendar(now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0'));
    }
    updatePreview();
  });

  // auto-open when the page is /check-date with ?open=1
  window.addEventListener('load', function () {
    if (document.body.dataset.standalone === '1' && new URLSearchParams(window.location.search).get('open') === '1') {
      openFlow({});
    }
  });
})();
