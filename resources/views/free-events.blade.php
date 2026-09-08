@extends('layouts.public')

@section('title', 'Free events — Kopi Rider')

@section('content')
<section class="section-pad" style="padding-top:40px;">
  <div class="wrap" style="max-width:760px;">
    <div class="reveal">
      <h2 class="section-title">☕ Free event? Here's how it works.</h2>
      <p class="section-sub">
        We bring the truck to your market, festival or community event
        <strong>at no charge</strong>. We make our money from coffee sales — and if we
        don't sell enough, a pre-agreed <span class="highlight">minimum guarantee</span> covers the difference.
      </p>
    </div>

    <div class="free-info reveal reveal-delay-1">
      <h3 style="font-size:20px;margin-bottom:10px;">The deal, plainly</h3>
      <ul class="check-list">
        <li>✔ <span><strong>No cost to you upfront.</strong> Zero. We take the risk.</span></li>
        <li>✔ <span>We sell coffee to your visitors — that's how we get paid.</span></li>
        <li>✔ <span>A minimum guarantee is agreed beforehand, based on your expected footfall.</span></li>
        <li>✔ <span>If sales fall short of the minimum, the organiser covers the difference. Nothing hidden.</span></li>
        <li>✔ <span>We handle everything: coffee, baristas, power, setup and cleanup.</span></li>
      </ul>
      <p style="font-size:15px;">
        Perfect for <strong>markets, festivals, school fairs, sports events and community
        gatherings</strong> across Bali.
      </p>
    </div>

    <div class="pkg-grid reveal reveal-delay-2">
      <div class="pkg-card">
        <h3>🎪 Step by step</h3>
        <ul>
          <li>1️⃣ You check a date &amp; send the request</li>
          <li>2️⃣ We talk on WhatsApp and agree the minimum</li>
          <li>3️⃣ Truck rolls in, coffee flows, everyone's happy</li>
          <li>4️⃣ Sales below minimum? The guarantee covers it</li>
        </ul>
      </div>
      <div class="pkg-card">
        <h3>📋 Good to know</h3>
        <ul>
          <li>⚡ We need power access or agreement to run a generator</li>
          <li>🚛 Truck needs roughly 6 × 3 m of level ground</li>
          <li>🗓️ Weekends book fast — ask early</li>
          <li>🤝 A human confirms every event — never an auto-reply</li>
        </ul>
      </div>
    </div>

    <div class="reveal reveal-delay-3" style="text-align:center;margin-top:26px;">
      <a href="{{ route('check-date') }}?open=1" class="btn btn-gold" data-open-booking>Check a date for my event</a>
      <p style="font-size:13px;color:#A6906F;margin-top:12px;">
        Send this page link straight to your event organiser.
      </p>
    </div>
  </div>
</section>
@endsection
