<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <meta name="theme-color" content="#F6ECD9">
  <meta name="description" content="Kopi Rider — a Bali coffee truck for weddings, private parties, markets and festivals. Check your date and get a quote via WhatsApp.">
  <title>@yield('title', 'Kopi Rider — Coffee Truck Booking · Bali')</title>
  @if (has_custom_favicon())
    <link rel="icon" href="{{ favicon_url() }}">
    <link rel="apple-touch-icon" href="{{ favicon_url() }}">
  @else
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="32x32">
    <link rel="icon" href="{{ asset('img/favicon.svg') }}" type="image/svg+xml">
    <link rel="icon" href="{{ asset('img/favicon-96.png') }}" sizes="96x96" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('img/apple-touch-icon.png') }}">
  @endif
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,340;0,9..144,480;0,9..144,600;1,9..144,420;1,9..144,500&family=Work+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  @stack('styles')
</head>
<body@yield('body')>

<div class="top">
  <div class="wrap top-inner">
    <a href="{{ route('home') }}" class="logo">
      <img src="{{ logo_url() }}" alt="Kopi Rider logo" class="logo-img" width="40" height="40">
      <div>
        <b>Kopi Rider</b>
        <span class="logo-sub">Coffee Truck · Bali</span>
      </div>
    </a>
    <div class="nav-links">
      <a class="nav-item" href="{{ route('packages') }}">Packages</a>
      <a class="nav-item" href="{{ route('free-events') }}">Free events</a>
      <a class="nav-item" href="{{ route('gallery') }}">Gallery</a>
      <a href="{{ route('login') }}" class="btn-login" aria-label="Login to admin area" title="Login">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
        <span>Login</span>
      </a>
      <a href="{{ route('check-date') }}" class="btn btn-sm" data-open-booking>
        <span class="label-full">Check your date</span>
        <span class="label-short">Check date</span>
      </a>
    </div>
  </div>
</div>

@yield('content')

<footer class="site-footer">
  <div class="wrap">
    <div style="border-top:1px solid #EFE3C4; padding-top:34px;">
      <a href="{{ route('home') }}" class="logo footer-brand">
        <img src="{{ logo_url() }}" alt="Kopi Rider logo" class="logo-img" width="46" height="46">
        <div>
          <b>Kopi Rider</b>
          <span class="logo-sub">Coffee Truck · Bali</span>
        </div>
      </a>
      <h3 style="font-size:20px;margin-top:26px;">📍 Want to try us first?</h3>
      <p style="color:#7C6650;">We post our daily location on Instagram &amp; TikTok.</p>
      <div class="social-links">
        <a href="{{ setting('instagram_url', 'https://instagram.com') }}" target="_blank" rel="noopener">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
          <span>Instagram</span>
        </a>
        <a href="{{ setting('tiktok_url', 'https://tiktok.com') }}" target="_blank" rel="noopener">
          <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64c.3 0 .6.05.88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>
          <span>TikTok</span>
        </a>
      </div>
      <nav class="footer-links" aria-label="Legal">
        <a href="{{ route('privacy-policy') }}">Privacy Policy</a>
        <span aria-hidden="true">·</span>
        <a href="{{ route('terms-and-conditions') }}">Terms &amp; Conditions</a>
      </nav>
      <div class="footnote">
        <span>© {{ date('Y') }} Kopi Rider · Bali · Halal certified · Rooftop for six ·
          <a href="{{ route('login') }}" style="text-decoration:none;">Admin login</a></span>
        <span class="dev-credit">Developed by
          <a href="https://digimagine.web.id" target="_blank" rel="noopener">Digimagine</a></span>
      </div>
    </div>
  </div>
</footer>

@include('partials.booking-flow')

@php
    $krPackages = ($packages ?? \App\Models\Package::active()->get())->map(fn ($p) => [
        'id' => $p->id,
        'name' => $p->name,
        'price' => $p->formatted_price,
        'features' => $p->features_list,
        'audience' => $p->audience,
    ])->values();
@endphp
<script>
  window.KR_PACKAGES = @json($krPackages);
</script>
<script src="{{ asset('js/app.js') }}" defer></script>
<script src="{{ asset('js/booking-flow.js') }}" defer></script>
@stack('scripts')
</body>
</html>
