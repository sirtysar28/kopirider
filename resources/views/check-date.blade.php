@extends('layouts.public')

@section('title', 'Check your date — Kopi Rider')

@section('body') data-standalone="1" @endsection

@section('content')
<section class="section-pad" style="padding-top:36px;">
  <div class="wrap">
    <div class="reveal">
      <h2 class="section-title">Check your date</h2>
      <p class="section-sub">
        Four quick steps — no typing until the last one. Green dates look available,
        yellow means someone is asking, red is already booked.
      </p>
    </div>

    <div class="pkg-card reveal reveal-delay-1" style="max-width:640px;margin:0 auto;text-align:center;padding:36px 24px;">
      <div style="font-size:52px;margin-bottom:12px;">📅</div>
      <h3 style="font-size:24px;margin-bottom:8px;">Start the booking flow</h3>
      <p style="color:#7C6650;margin-bottom:22px;font-size:15px;">
        Pick your date, tell us about the event, see the prices, and send the request —
        it takes less than a minute.
      </p>
      <button class="btn btn-gold" data-open-booking>Start — check my date</button>
      <p style="font-size:12.5px;color:#A6906F;margin-top:14px;">
        You can also link straight to this page from social media — it works on its own.
      </p>
    </div>

    <div class="pkg-grid" style="max-width:640px;margin:26px auto 0;">
      <div class="pkg-card reveal reveal-delay-2">
        <h3>What happens next?</h3>
        <ul>
          <li>1️⃣ Pick a date — you instantly see if it looks free</li>
          <li>2️⃣ Choose your event type &amp; guest range (buttons only)</li>
          <li>3️⃣ See packages &amp; starting prices — or the free option</li>
          <li>4️⃣ Leave your name + WhatsApp, we confirm personally</li>
        </ul>
        <p style="font-size:13px;color:#7C6650;">
          We never auto-confirm bookings — a real person always replies on WhatsApp.
        </p>
      </div>
    </div>
  </div>
</section>
@endsection
