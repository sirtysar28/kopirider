<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Admin') — Kopi Rider</title>
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
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,480&family=Work+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
<div class="admin-shell">

  <!-- Mobile top bar (only visible on small screens) -->
  <header class="mobile-topbar">
    <div class="brand">
      <img src="{{ logo_url() }}" alt="Kopi Rider logo" style="width:34px;height:34px;border-radius:10px;">
      <div>
        <b>Kopi Rider</b>
        <span>ADMIN</span>
      </div>
    </div>
    <button type="button" class="nav-toggle" aria-label="Open menu" aria-controls="adminSidebar" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
  </header>

  <aside class="sidebar" id="adminSidebar">
    <div class="brand">
      <img src="{{ logo_url() }}" alt="Kopi Rider logo" style="width:38px;height:38px;border-radius:11px;">
      <div>
        <b>Kopi Rider</b>
        <span>ADMIN</span>
      </div>
    </div>

    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
      <span class="ico">📊</span> Dashboard
    </a>
    <a class="nav-link {{ request()->routeIs('admin.leads.*') ? 'active' : '' }}" href="{{ route('admin.leads.index') }}">
      <span class="ico">📝</span> Leads
    </a>
    <a class="nav-link {{ request()->routeIs('admin.calendar.*') ? 'active' : '' }}" href="{{ route('admin.calendar.index') }}">
      <span class="ico">📅</span> Calendar
    </a>
    <a class="nav-link {{ request()->routeIs('admin.packages.*') ? 'active' : '' }}" href="{{ route('admin.packages.index') }}">
      <span class="ico">💼</span> Packages
    </a>
    <a class="nav-link {{ request()->routeIs('admin.gallery.*') ? 'active' : '' }}" href="{{ route('admin.gallery.index') }}">
      <span class="ico">🖼️</span> Media
    </a>
    <a class="nav-link {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}" href="{{ route('admin.analytics.index') }}">
      <span class="ico">📈</span> Analytics
    </a>
    <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
      <span class="ico">⚙️</span> Settings
    </a>

    <div class="spacer"></div>

    <a class="nav-link" href="{{ route('home') }}" target="_blank">
      <span class="ico">🌍</span> View site
    </a>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="nav-link logout" style="width:100%;border:none;background:none;cursor:pointer;font-family:inherit;font-size:14px;">
        <span class="ico">🚪</span> Sign out
      </button>
    </form>
  </aside>

  <main class="main">
    @if (session('success'))
      <div class="flash success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
      <div class="flash error">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
      <div class="flash error">
        {{ $errors->first() }}
        @if ($errors->count() > 1)
          <span class="muted">(+{{ $errors->count() - 1 }} more)</span>
        @endif
      </div>
    @endif

    @yield('content')

    <footer class="admin-footer">
      <span>© {{ date('Y') }} Kopi Rider · Developed by
        <a href="https://digimagine.web.id" target="_blank" rel="noopener">Digimagine</a></span>
    </footer>
  </main>

</div>

<div class="nav-backdrop" aria-hidden="true"></div>

<script>
  (function () {
    var body = document.body;
    var toggle = document.querySelector('.nav-toggle');
    var backdrop = document.querySelector('.nav-backdrop');

    function closeNav() {
      body.classList.remove('nav-open');
      if (toggle) toggle.setAttribute('aria-expanded', 'false');
    }

    if (toggle) {
      toggle.addEventListener('click', function () {
        var open = body.classList.toggle('nav-open');
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      });
    }
    if (backdrop) backdrop.addEventListener('click', closeNav);

    // Close the drawer after tapping any navigation link
    document.querySelectorAll('.sidebar a.nav-link').forEach(function (link) {
      link.addEventListener('click', closeNav);
    });

    // Reset state when switching back to desktop
    window.addEventListener('resize', function () {
      if (window.innerWidth > 860) closeNav();
    });
  })();
</script>
</body>
</html>
