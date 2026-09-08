@extends('layouts.public')

@section('title', 'Kopi Rider — Coffee Truck Booking · Bali')

@section('content')

<!-- ===== HERO ===== -->
<section class="hero-banner" id="heroBanner">
  <div class="media-slot">
    @if ($heroMedia)
      <img src="{{ Storage::url($heroMedia->path) }}" alt="Kopi Rider coffee truck at an event" loading="eager">
    @else
      <div class="media-placeholder">
        <span class="big">📸</span>
        <span>Our truck photo goes here — coming soon</span>
        <span style="font-size:12px;opacity:.55;">We launch with real photos, never stock images.</span>
      </div>
    @endif
  </div>
  <div class="overlay"></div>
  <div class="hero-content">
    <span class="status-badge">☕ {{ setting('hero_status', 'open today · Pererenan') }}</span>
    <h1>Roasted in Bali, <em>right to your event.</em></h1>
    <p class="sub-text">
      We bring the truck, the baristas, and the rooftop deck — from private weddings
      to beach markets. No charge for the truck at festivals; we just sell coffee.
    </p>
    <div class="cta-group">
      <a href="{{ route('check-date') }}" class="btn btn-gold" data-open-booking>Check your date</a>
      <a href="#packages" class="btn" style="background:transparent;border-color:rgba(255,255,255,.4);color:#fff;box-shadow:none;">See packages</a>
    </div>
  </div>
  <a href="#packages" class="scroll-hint">Scroll</a>
</section>

<!-- ===== PACKAGES ===== -->
<section class="section-pad" id="packages">
  <div class="wrap">
    <div class="reveal">
      <h2 class="section-title">Packages &amp; the free option</h2>
      <p class="section-sub">
        Weddings, private parties, markets and festivals — one truck, one crew, honestly priced.
        Starting prices are visible before you ever message us.
      </p>
    </div>

    <div class="pkg-grid">
      @foreach ($packages->where('audience', 'paid') as $package)
        <div class="pkg-card reveal {{ $loop->iteration === 1 ? 'reveal-delay-1' : 'reveal-delay-2' }}">
          <h3>{{ $package->name }}</h3>
          <div class="pkg-price">
            From {{ $package->formatted_price }}
            <small>Starting price — final quote confirmed by our team</small>
          </div>
          <ul>
            @foreach ($package->features_list as $feature)
              <li>✅ {{ $feature }}</li>
            @endforeach
          </ul>
          <a href="{{ route('check-date') }}" class="btn btn-sm btn-block" data-open-booking>Book this</a>
        </div>
      @endforeach

      <div class="pkg-card reveal reveal-delay-3" style="border-color:#C98A34;">
        <h3>Market / Festival <span style="font-size:14px;font-weight:400;color:#7C6650;">(free option)</span></h3>
        <div class="pkg-price">🎪 No upfront fee<small>Organiser guarantee covers the minimum</small></div>
        <p style="font-size:14px;margin:10px 0;">
          We come at no charge and make our money selling coffee. If sales fall short of the
          agreed minimum, the organiser covers the difference.
        </p>
        <ul>
          <li>✅ No upfront fee, ever</li>
          <li>✅ Minimum guarantee agreed beforehand</li>
          <li>✅ Same great coffee, zero risk</li>
        </ul>
        <a href="{{ route('free-events') }}" class="btn btn-sm btn-line btn-block">How the free option works</a>
      </div>
    </div>

    <p style="font-size:14px;color:#7C6650;margin-top:12px;text-align:center;" class="reveal">
      All final pricing is confirmed on WhatsApp after we understand your event.
    </p>
  </div>
</section>

<!-- ===== WHY US ===== -->
<section class="section-pad" style="background:#fff;border-radius:40px 40px 0 0;">
  <div class="wrap">
    <div class="reveal">
      <h2 class="section-title">Why book the truck?</h2>
      <p class="section-sub">One truck, a small crew, and coffee people actually queue for.</p>
    </div>
    <div class="pkg-grid">
      <div class="pkg-card reveal reveal-delay-1">
        <h3>☕ Real Balinese coffee</h3>
        <p style="font-size:14px;">Beans roasted locally in Bali, espresso drinks made to order by real baristas — not a vending machine with wheels.</p>
      </div>
      <div class="pkg-card reveal reveal-delay-2">
        <h3>🛻 We come to you</h3>
        <p style="font-size:14px;">Villa, beach, rice-field wedding, market square — if the truck fits, the coffee flows. Setup and cleanup are on us.</p>
      </div>
      <div class="pkg-card reveal reveal-delay-3">
        <h3>🪜 The rooftop deck</h3>
        <p style="font-size:14px;">Our truck carries a rooftop deck for six — the most photographed coffee corner at any event.</p>
      </div>
    </div>
  </div>
</section>
@endsection
