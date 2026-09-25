@extends('layouts.public')

@section('title', 'Packages — Kopi Rider')

@section('content')
<section class="section-pad" style="padding-top:40px;">
  <div class="wrap">
    <div class="reveal">
      <h2 class="section-title">Packages</h2>
      <p class="section-sub">
        Starting prices are shown up front because nobody should have to ask "how much?"
        twice. Final pricing is always confirmed personally on WhatsApp.
      </p>
    </div>

    <div class="pkg-grid">
      @forelse ($packages as $package)
        <div class="pkg-card reveal {{ $loop->iteration % 3 === 2 ? 'reveal-delay-1' : ($loop->iteration % 3 === 0 ? 'reveal-delay-2' : '') }}">
          @if ($package->audience === 'free')
            <h3>{{ $package->name }} <span style="font-size:14px;font-weight:400;color:#7C6650;">(free option)</span></h3>
          @else
            <h3>{{ $package->name }}</h3>
          @endif
          <div class="pkg-price">
            {{ $package->formatted_price }}
            <small>Starting price — final quote confirmed by our team</small>
          </div>
          @if ($package->description)
            <p style="font-size:14px;margin:10px 0;">{{ $package->description }}</p>
          @endif
          <ul>
            @foreach ($package->features_list as $feature)
              <li>✅ {{ $feature }}</li>
            @endforeach
          </ul>
          <a href="{{ route('check-date') }}" class="btn btn-sm btn-block" data-open-booking>Check date for this</a>
        </div>
      @empty
        <div class="gallery-empty">
          <span class="big">☕</span>
          Packages are being updated — check back soon, or ask us directly on WhatsApp.
        </div>
      @endforelse
    </div>

    <p style="font-size:14px;color:#7C6650;margin-top:12px;" class="reveal">
      Running a market, festival or community event?
      <a href="{{ route('free-events') }}" style="color:#8B4226;font-weight:700;">See how the free option works →</a>
    </p>

    {{-- Price note — legal text (Update 21-09-2026) --}}
    <div class="legal-note reveal">
      <h3>💡 About our prices</h3>
      <p>
        All prices are in Indonesian Rupiah (IDR) and include tax. The prices shown are starting
        prices — your final price is confirmed in writing on WhatsApp once we understand your event.
        If you sit on the rooftop deck, a service fee of 10–50% depending on time of day or event
        is added for bringing your food up. The travel surcharge depends on where the truck is the
        day before your event and is shown in your quote. Bookings made less than 6 hours before
        the event cost 50% extra. You always see the full price before you pay.
      </p>
    </div>

    {{-- Allergen information — legal text (Update 21-09-2026) --}}
    <div class="legal-note reveal" id="allergen-info">
      <h3>🥜 Allergen information</h3>
      <p>
        Allergens are listed on our menu. Our food and drinks are made in a small space where common
        allergens such as milk, gluten, nuts, soy and eggs are handled, and some food comes from
        partner bakeries and restaurants, so we can’t guarantee that any item is free from a specific
        allergen. If you have an allergy or a special request, please ask our staff before you order.
        For events, tell us in writing at least one week before, with clear and specific instructions —
        we can only take allergies into account if you tell us. For special requests at events, we can
        put up a sign to inform your guests.
      </p>
    </div>

    {{-- Rooftop deck safety rules — legal text (Update 21-09-2026) --}}
    <div class="legal-note reveal" id="rooftop-safety">
      <h3>🪜 Rooftop deck — safety rules</h3>
      <p>
        Our rooftop deck sits on top of the truck and is reached by steep stairs. It’s a lovely
        spot — please treat it like a balcony.
      </p>
      <ol class="rooftop-rules">
        <li>No more than 7 people, or 490 kg in total, on the deck at a time — whichever limit is reached first.</li>
        <li>One person on the stairs at a time. Hold the handrail and go up with empty hands — we’ll pass your drinks up.</li>
        <li>Don’t sit on or lean over the railing, and don’t stand on chairs or tables.</li>
        <li>Children must be with an adult at all times.</li>
        <li>No glass and no smoking on the deck.</li>
        <li>Guests who appear intoxicated can’t go up.</li>
        <li>The deck closes in rain, strong wind or lightning — wet steel is slippery.</li>
        <li>If the rules are not followed, we will close the deck for everyone’s safety. When our staff ask you to come down, please do so straight away — their decision is final.</li>
        <li>The truck and deck are private property. We don’t have security staff, so if someone won’t come down when asked, we will have to ask the police for help.</li>
      </ol>
      <p style="margin-bottom:0;">Thank you for helping us keep everyone safe.</p>
    </div>
  </div>
</section>
@endsection
