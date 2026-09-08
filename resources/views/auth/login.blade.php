<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <meta name="theme-color" content="#F6ECD9">
  <title>Staff login — Kopi Rider</title>
  @if (has_custom_favicon())
    <link rel="icon" href="{{ favicon_url() }}">
  @else
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="32x32">
    <link rel="icon" href="{{ asset('img/favicon.svg') }}" type="image/svg+xml">
    <link rel="icon" href="{{ asset('img/favicon-96.png') }}" sizes="96x96" type="image/png">
  @endif
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,480;1,9..144,500&family=Work+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; margin: 0; }
    body {
      min-height: 100vh; display: flex; align-items: center; justify-content: center;
      background: #F6ECD9; color: #2A1B12;
      font-family: 'Work Sans', system-ui, sans-serif; line-height: 1.5;
      padding: 20px;
      background-image: radial-gradient(circle at 15% 20%, rgba(201,138,52,.14), transparent 40%),
                        radial-gradient(circle at 85% 85%, rgba(139,66,38,.10), transparent 45%);
    }
    .login-card {
      background: #fff; border: 1px solid #EFE3C4; border-radius: 28px;
      padding: 40px 34px; width: 100%; max-width: 420px;
      box-shadow: 0 30px 60px -30px rgba(42,27,18,.35);
      animation: cardIn .6s cubic-bezier(.2,.9,.3,1.1) both;
    }
    @keyframes cardIn { from { opacity: 0; transform: translateY(26px) scale(.98); } to { opacity: 1; transform: translateY(0) scale(1); } }
    .logo { display: flex; align-items: center; gap: 10px; text-decoration: none; color: #2A1B12; margin-bottom: 26px; }
    .logo-mark { background: #8B4226; border-radius: 50%; width: 42px; height: 42px; display: grid; place-items: center; color: #fff; font-weight: 700; font-size: 18px; }
    .logo b { font-family: 'Fraunces', serif; font-size: 22px; display: block; }
    .logo span { font-size: 11px; letter-spacing: .12em; opacity: .7; display: block; margin-top: -2px; }
    h1 { font-family: 'Fraunces', serif; font-weight: 480; font-size: 26px; margin-bottom: 4px; }
    .sub { color: #7C6650; font-size: 14px; margin-bottom: 24px; }
    .field { margin-bottom: 16px; }
    .field label { display: block; font-size: 13px; font-weight: 700; color: #5E2A16; margin-bottom: 6px; }
    .input-wrap { position: relative; }
    .input-wrap input {
      width: 100%; padding: 14px 52px 14px 18px; border-radius: 60px;
      border: 1px solid #E4D2A8; font-size: 15px; font-family: inherit;
      background: #FDFBF6; color: #2A1B12; transition: border-color .2s, box-shadow .2s;
    }
    .input-wrap input:focus { outline: none; border-color: #8B4226; box-shadow: 0 0 0 3px rgba(139,66,38,.15); }
    /* ---- the password peek toggle ---- */
    .peek-btn {
      position: absolute; right: 7px; top: 50%; transform: translateY(-50%);
      width: 38px; height: 38px; border-radius: 50%; border: none; cursor: pointer;
      background: #EFE0BE; color: #5E2A16; display: grid; place-items: center;
      transition: background .2s, transform .2s; padding: 0;
    }
    .peek-btn:hover { background: #E4D2A8; transform: scale(1.06); }
    .peek-btn:active { transform: scale(.94); }
    .peek-btn svg { width: 19px; height: 19px; }
    .peek-btn .eye-off { display: none; }
    .peek-btn.showing .eye-on { display: none; }
    .peek-btn.showing .eye-off { display: block; }
    .btn {
      width: 100%; min-height: 52px; border-radius: 100px; border: 1px solid #8B4226;
      background: #8B4226; color: #fff; font-weight: 700; font-size: 15px;
      font-family: inherit; cursor: pointer; margin-top: 6px;
      transition: background .25s, transform .25s, box-shadow .25s;
      box-shadow: 0 10px 24px -16px rgba(42,27,18,.35);
    }
    .btn:hover { background: #5E2A16; transform: translateY(-2px); }
    .remember { display: flex; align-items: center; gap: 8px; font-size: 13.5px; color: #7C6650; margin: 4px 0 2px; }
    .remember input { accent-color: #8B4226; width: 16px; height: 16px; cursor: pointer; }
    .error-box {
      background: #F9E3DC; border: 1px solid #D98E76; color: #8B4226;
      border-radius: 14px; padding: 11px 16px; font-size: 13.5px; margin-bottom: 16px;
      animation: shake .45s ease;
    }
    @keyframes shake { 0%,100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 75% { transform: translateX(5px); } }
    .backlink { display: block; text-align: center; margin-top: 18px; font-size: 13px; color: #A6906F; text-decoration: none; }
    .backlink:hover { color: #8B4226; }
    .dev-credit { text-align: center; margin-top: 10px; font-size: 12.5px; color: #A6906F; }
    .dev-credit a { color: #8B4226; font-weight: 800; text-decoration: none; }
    .dev-credit a:hover { text-decoration: underline; }
  </style>
</head>
<body>
  <div class="login-card">
    <a href="{{ route('home') }}" class="logo">
      <img src="{{ logo_url() }}" alt="Kopi Rider logo" style="width:42px;height:42px;border-radius:12px;">
      <div>
        <b>Kopi Rider</b>
        <span>STAFF AREA</span>
      </div>
    </a>

    <h1>Welcome back</h1>
    <p class="sub">Sign in to manage the calendar, leads and payments.</p>

    @if ($errors->any())
      <div class="error-box">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login.attempt') }}">
      @csrf
      <div class="field">
        <label for="email">E-mail</label>
        <div class="input-wrap">
          <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@kopirider.id" required autofocus autocomplete="email">
        </div>
      </div>

      <div class="field">
        <label for="password">Password</label>
        <div class="input-wrap">
          <input type="password" id="password" name="password" placeholder="••••••••" required autocomplete="current-password">
          {{-- password peek toggle --}}
          <button type="button" class="peek-btn" id="peekBtn" aria-label="Show password" title="Show password">
            <svg class="eye-on" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
              <circle cx="12" cy="12" r="3"/>
            </svg>
            <svg class="eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
              <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
              <path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"/>
              <line x1="1" y1="1" x2="23" y2="23"/>
            </svg>
          </button>
        </div>
      </div>

      <label class="remember">
        <input type="checkbox" name="remember" value="1"> Remember me on this device
      </label>

      <button type="submit" class="btn">Sign in</button>
    </form>

    <a href="{{ route('home') }}" class="backlink">← Back to the website</a>
    <p class="dev-credit">Developed by
      <a href="https://digimagine.web.id" target="_blank" rel="noopener">Digimagine</a></p>
  </div>

  <script>
    (function () {
      var input = document.getElementById('password');
      var btn = document.getElementById('peekBtn');
      btn.addEventListener('click', function () {
        var show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        btn.classList.toggle('showing', show);
        btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        btn.title = show ? 'Hide password' : 'Show password';
        // keep focus in the field so typing continues smoothly
        input.focus({ preventScroll: true });
      });
    })();
  </script>
</body>
</html>
