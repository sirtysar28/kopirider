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
  </div>
</section>
@endsection
