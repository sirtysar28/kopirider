{{-- The 4-step booking flow modal — shared by the landing page and /check-date --}}
<div class="modal-overlay" id="bookingModal" aria-modal="true" role="dialog" aria-label="Check your date">
  <div class="modal">
    <button class="modal-close" data-close-booking aria-label="Close">✕</button>
    <div class="progress" id="progressBar">
      <span class="progress-step active"></span>
      <span class="progress-step"></span>
      <span class="progress-step"></span>
      <span class="progress-step"></span>
    </div>

    {{-- Step 1: date --}}
    <div class="flow-step active" data-step="1">
      <h2>Pick your date</h2>
      <p class="sub">Tap a date — we'll tell you straight away if it looks free.</p>
      <div class="cal-head">
        <button class="cal-nav" id="calPrev" aria-label="Previous month">←</button>
        <div class="cal-title" id="calTitle">&nbsp;</div>
        <button class="cal-nav" id="calNext" aria-label="Next month">→</button>
      </div>
      <div class="flow-calendar" id="calendarGrid"></div>
      <div class="cal-legend">
        <span><i style="background:#5B6B45;"></i> Available</span>
        <span><i style="background:#C98A34;"></i> Someone is asking</span>
        <span><i style="background:#8B4226;"></i> Booked</span>
      </div>
      <div class="cal-hint" id="calHint"></div>
      <div class="flow-footer">
        <span style="font-size:13px;color:#7C6650;" id="selectedDateDisplay">No date selected</span>
        <button class="btn btn-sm" id="step1Next" disabled>Next →</button>
      </div>
    </div>

    {{-- Step 2: event type + guests --}}
    <div class="flow-step" data-step="2">
      <h2>Event &amp; guests</h2>
      <p class="sub">What are you planning? Just tap — no typing needed.</p>
      <p class="flow-question">What type of event is it?</p>
      <div class="flow-options" id="eventTypeGroup">
        <button data-value="wedding">💒 Wedding</button>
        <button data-value="private">🎉 Private party</button>
        <button data-value="corporate">🏢 Corporate</button>
        <button data-value="market">🛒 Market</button>
        <button data-value="festival">🎪 Festival</button>
        <button data-value="community">🏘️ Community</button>
        <button data-value="other">✨ Other</button>
      </div>
      <p class="flow-question">Approximately how many guests?</p>
      <div class="flow-options" id="guestRangeGroup">
        <button data-value="<50">Under 50</button>
        <button data-value="50-100">50–100</button>
        <button data-value="100-200">100–200</button>
        <button data-value="200-300">200–300</button>
        <button data-value="300-500">300–500</button>
        <button data-value="500+">500+</button>
      </div>
      <div class="flow-footer">
        <button class="back" data-prev>← Back</button>
        <button class="btn btn-sm" id="step2Next" disabled>Next →</button>
      </div>
    </div>

    {{-- Step 3: packages / free option --}}
    <div class="flow-step" data-step="3">
      <h2 id="step3Title">Your package</h2>
      <p class="sub">Starting prices below — final pricing is confirmed by our team based on your details.</p>
      <div id="step3Content"></div>
      <div class="flow-footer">
        <button class="back" data-prev>← Back</button>
        <button class="btn btn-sm" id="step3Next" disabled>Next →</button>
      </div>
    </div>

    {{-- Step 4: contact --}}
    <div class="flow-step" data-step="4">
      <h2>Almost done</h2>
      <p class="sub">Just your name and WhatsApp so we can confirm.</p>
      <div style="display:flex;flex-direction:column;gap:16px;margin:16px 0;">
        <div class="field">
          <label for="leadName">Your name</label>
          <input type="text" id="leadName" placeholder="e.g. Made Wirawan" autocomplete="name" maxlength="120">
        </div>
        <div class="field">
          <label for="leadWhatsApp">WhatsApp number</label>
          <input type="tel" id="leadWhatsApp" placeholder="+62 812 3456 7890 (with country code)" autocomplete="tel" maxlength="30">
        </div>
        <div class="msg-preview">
          <strong>Pre-written message:</strong><br>
          <span id="previewMessage">Hi! I'd like to book the truck for [date], a [event type], around [guests] guests.</span>
        </div>
      </div>
      <div class="flow-footer">
        <button class="back" data-prev>← Back</button>
        <button class="btn" id="submitLead">Send request</button>
      </div>
    </div>

    {{-- Step 5: success (not in the progress bar) --}}
    <div class="flow-step" data-step="5">
      <div class="lead-success">
        <div class="check">✓</div>
        <h2 style="margin-bottom:8px;">Request saved!</h2>
        <p class="sub" style="margin-bottom:6px;">
          Your reference is <strong id="leadRef">KR-0000</strong>.
        </p>
        <p class="sub" style="margin-bottom:22px;">
          WhatsApp should open in a new tab — just press send there.
          If it doesn't open, don't worry: we already have your request and will reach out.
        </p>
        <button class="btn btn-line btn-sm" data-close-booking>Close</button>
      </div>
    </div>
  </div>
</div>
