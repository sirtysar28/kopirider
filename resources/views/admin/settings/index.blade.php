@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<div class="page-head">
  <div>
    <h1>Settings</h1>
    <p class="muted">WhatsApp number powers every redirect and chat button on the site.</p>
  </div>
</div>

<form method="POST" action="{{ route('admin.settings.update') }}">
  @csrf

  <div class="card" style="max-width:680px;">
    <h2 style="font-size:20px;margin-bottom:14px;">🌐 General</h2>
    <div style="display:flex;flex-direction:column;gap:16px;">
      @foreach ($general as $key => $label)
        <div class="field">
          <label for="{{ $key }}">{{ $label }}</label>
          <input type="text" id="{{ $key }}" name="{{ $key }}" value="{{ old($key, $values[$key] ?? '') }}" placeholder="{{ $key === 'whatsapp_number' ? '6281234567890' : '' }}">
        </div>
      @endforeach
    </div>
  </div>

  <div class="card" style="max-width:680px;">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;margin-bottom:6px;">
      <h2 style="font-size:20px;">💳 Payments — Midtrans</h2>
      <span class="badge {{ $midtransEnabled ? 'confirmed' : 'lost' }}">
        {{ $midtransEnabled ? 'ENABLED' : 'DISABLED' }}
      </span>
    </div>
    <p class="muted" style="margin-bottom:16px;">
      When enabled, staff can generate Midtrans payment links (deposit / balance)
      straight from a lead. When disabled, only bank transfer records are available —
      no API calls are made at all.
    </p>

    <div style="display:flex;flex-direction:column;gap:16px;">
      @foreach ($payment as $key => $conf)
        <div class="field">
          @if (($conf['type'] ?? 'text') === 'toggle')
            <label class="check-toggle" for="{{ $key }}" style="font-size:15px;">
              <input type="checkbox" id="{{ $key }}" name="{{ $key }}" value="1"
                     {{ old($key, $values[$key] ?? '0') === '1' ? 'checked' : '' }}>
              {{ $conf['label'] }}
            </label>
          @elseif (($conf['type'] ?? 'text') === 'select')
            <label for="{{ $key }}">{{ $conf['label'] }}</label>
            <select id="{{ $key }}" name="{{ $key }}">
              @foreach ($conf['options'] as $val => $name)
                <option value="{{ $val }}" {{ old($key, $values[$key] ?? 'sandbox') === $val ? 'selected' : '' }}>{{ $name }}</option>
              @endforeach
            </select>
          @elseif (($conf['type'] ?? 'text') === 'password')
            <label for="{{ $key }}">{{ $conf['label'] }}</label>
            <input type="password" id="{{ $key }}" name="{{ $key }}" value="{{ old($key, $values[$key] ?? '') }}" autocomplete="new-password">
          @elseif (($conf['type'] ?? 'text') === 'textarea')
            <label for="{{ $key }}">{{ $conf['label'] }}</label>
            <textarea id="{{ $key }}" name="{{ $key }}" rows="4" placeholder="{{ $conf['placeholder'] ?? '' }}">{{ old($key, $values[$key] ?? '') }}</textarea>
          @else
            <label for="{{ $key }}">{{ $conf['label'] }}</label>
            <input type="text" id="{{ $key }}" name="{{ $key }}" value="{{ old($key, $values[$key] ?? '') }}">
          @endif
        </div>
      @endforeach
    </div>

    <p class="muted" style="margin-top:12px;">
      🔔 Notification URL for the Midtrans dashboard:
      <code style="background:#FBF6EA;padding:3px 8px;border-radius:8px;">{{ rtrim(config('app.url'), '/') }}/api/midtrans/notification</code>
      — payments update automatically when customers pay.
    </p>
  </div>

  {{-- ============ SMTP / E-MAIL ============ --}}
  <div class="card" style="max-width:680px;">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;margin-bottom:6px;">
      <h2 style="font-size:20px;">📧 E-mail — SMTP</h2>
      <span class="badge {{ $smtpEnabled ? 'confirmed' : 'lost' }}">
        {{ $smtpEnabled ? 'ENABLED' : 'DISABLED' }}
      </span>
    </div>
    <p class="muted" style="margin-bottom:16px;">
      Used for the <b>forgot password</b> e-mail and future notifications.
      Gmail example: host <code>smtp.gmail.com</code>, port <code>587</code>, TLS,
      username = your Gmail address, password = an <a href="https://myaccount.google.com/apppasswords" target="_blank" rel="noopener">App&nbsp;Password</a>.
    </p>

    <div style="display:flex;flex-direction:column;gap:16px;">
      @foreach ($smtp as $key => $conf)
        <div class="field">
          @if (($conf['type'] ?? 'text') === 'toggle')
            <label class="check-toggle" for="{{ $key }}" style="font-size:15px;">
              <input type="checkbox" id="{{ $key }}" name="{{ $key }}" value="1"
                     {{ old($key, $values[$key] ?? '0') === '1' ? 'checked' : '' }}>
              {{ $conf['label'] }}
            </label>
          @elseif (($conf['type'] ?? 'text') === 'select')
            <label for="{{ $key }}">{{ $conf['label'] }}</label>
            <select id="{{ $key }}" name="{{ $key }}">
              @foreach ($conf['options'] as $val => $name)
                <option value="{{ $val }}" {{ old($key, $values[$key] ?? 'tls') === $val ? 'selected' : '' }}>{{ $name }}</option>
              @endforeach
            </select>
          @elseif (($conf['type'] ?? 'text') === 'password')
            <label for="{{ $key }}">{{ $conf['label'] }}</label>
            <input type="password" id="{{ $key }}" name="{{ $key }}" value="" autocomplete="new-password" placeholder="••••••••••••">
          @else
            <label for="{{ $key }}">{{ $conf['label'] }}</label>
            <input type="text" id="{{ $key }}" name="{{ $key }}" value="{{ old($key, $values[$key] ?? '') }}" placeholder="{{ $conf['placeholder'] ?? '' }}">
          @endif
        </div>
      @endforeach
    </div>
  </div>

  <div class="card" style="max-width:680px;">
    <h2 style="font-size:20px;margin-bottom:14px;">📄 Legal pages</h2>
    <p class="muted" style="margin-bottom:16px;">
      Content for the public <b>Privacy Policy</b> and <b>Terms &amp; Conditions</b> pages.
      Simple HTML is allowed (<code>&lt;h3&gt;</code>, <code>&lt;p&gt;</code>, <code>&lt;ul&gt;</code>, <code>&lt;li&gt;</code>, <code>&lt;b&gt;</code>, <code>&lt;a&gt;</code>).
      Use <code>{email}</code> anywhere to print the contact e-mail.
      Leave a field empty to keep the built-in default text.
    </p>
    <div style="display:flex;flex-direction:column;gap:16px;">
      @foreach ($legal as $key => $conf)
        @php($slug = $key === 'privacy_policy' ? 'privacy-policy' : 'terms-and-conditions')
        <div class="field">
          <label for="{{ $key }}">{{ $conf['label'] }}</label>
          <textarea id="{{ $key }}" name="{{ $key }}" rows="{{ $conf['rows'] ?? 10 }}" placeholder="Leave empty to use the default text…">{{ old($key, $values[$key] ?? '') }}</textarea>
          <p class="muted" style="font-size:12.5px;margin:2px 0 0;">
            Public page:
            <a href="{{ route($slug) }}" target="_blank" rel="noopener">{{ url($slug) }}</a>
          </p>
        </div>
      @endforeach
    </div>
  </div>

  <button type="submit" class="btn">Save settings</button>
</form>

{{-- Separate self-contained forms below (no nesting — HTML forbids it and
     browsers would silently drop the inner <form> tags, breaking uploads) --}}

  <div class="card" style="max-width:680px;">
    <h2 style="font-size:20px;margin-bottom:14px;">📨 Send a test e-mail</h2>
    <p class="muted" style="margin-bottom:16px;">
      After enabling &amp; saving SMTP above, send a branded test e-mail to verify
      the credentials actually work. It arrives in full HTML — logo, header &amp; footer.
    </p>
    <form method="POST" action="{{ route('admin.settings.test-email') }}">
      @csrf
      <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
        <div class="field" style="flex:1;min-width:240px;">
          <label for="test_email">Recipient</label>
          <input type="email" id="test_email" name="test_email" required placeholder="{{ auth()->user()->email }}" value="{{ old('test_email', auth()->user()->email) }}">
        </div>
        <button type="submit" class="btn">📨 Send test e-mail</button>
      </div>
    </form>
  </div>

  <div class="card" style="max-width:680px;">
    <h2 style="font-size:20px;margin-bottom:14px;">🖼️ Logo &amp; favicon</h2>
    <p class="muted" style="margin-bottom:16px;">
      Upload your own logo and browser favicon (tab icon).
      Recommended: logo square PNG/SVG (min 96×96px), favicon square PNG/SVG/ICO (32×32 – 96×96px).
      Leave empty to keep the current one.
    </p>
    <form method="POST" action="{{ route('admin.settings.branding') }}" enctype="multipart/form-data">
      @csrf
      <div style="display:flex;flex-direction:column;gap:16px;">
        <div class="field">
          <label for="logo">Current logo</label>
          <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
            <img src="{{ logo_url() }}" alt="Current logo" style="width:56px;height:56px;border-radius:14px;border:1px solid #EFE3C4;object-fit:cover;">
            <input type="file" id="logo" name="logo" accept="image/*" style="max-width:320px;">
          </div>
        </div>
        <div class="field">
          <label for="favicon">Favicon</label>
          <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
            <img src="{{ favicon_url() }}" alt="Current favicon" style="width:32px;height:32px;border-radius:8px;border:1px solid #EFE3C4;object-fit:contain;background:#fff;">
            <input type="file" id="favicon" name="favicon" accept=".png,.svg,.ico,image/*" style="max-width:320px;">
          </div>
        </div>
      </div>
      <button type="submit" class="btn" style="margin-top:16px;">Upload &amp; apply</button>
    </form>
  </div>

  <div class="card" style="max-width:680px;">
    <h2 style="font-size:20px;margin-bottom:14px;">🔐 Change password</h2>
    <p class="muted" style="margin-bottom:16px;">
      Changes the password of the account you are signed in with ({{ auth()->user()->email }}).
    </p>
    <form method="POST" action="{{ route('admin.settings.password') }}">
      @csrf
      <div style="display:flex;flex-direction:column;gap:16px;max-width:420px;">
        <div class="field">
          <label for="current_password">Current password</label>
          <input type="password" id="current_password" name="current_password" required autocomplete="current-password">
        </div>
        <div class="field">
          <label for="password">New password</label>
          <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password" placeholder="Minimum 8 characters">
        </div>
        <div class="field">
          <label for="password_confirmation">Repeat new password</label>
          <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" autocomplete="new-password">
        </div>
      </div>
      <button type="submit" class="btn" style="margin-top:16px;">Update password</button>
    </form>
  </div>

  <div class="card" style="max-width:680px;">
    <h2 style="font-size:20px;margin-bottom:14px;">👥 Staff users</h2>
    <p class="muted" style="margin-bottom:16px;">
      Everyone listed here can sign in to the admin panel with their own e-mail and password.
      <b>Super admins</b> can additionally <b>delete leads</b> and manage roles — regular <b>staff</b> cannot.
    </p>

    <div class="table-wrap table-cards">
      <table class="list">
        <thead>
          <tr><th>Name</th><th>E-mail</th><th>Role</th><th>Added</th><th></th></tr>
        </thead>
        <tbody>
          @foreach ($users as $user)
            <tr>
              <td data-label="Name" style="font-weight:700;">{{ $user->name }}
                @if ($user->id === auth()->id())
                  <span class="badge confirmed">you</span>
                @endif
              </td>
              <td data-label="E-mail">{{ $user->email }}</td>
              <td data-label="Role">
                <span class="badge {{ $user->isSuperAdmin() ? 'superadmin' : 'staffrole' }}">
                  {{ $user->isSuperAdmin() ? '⭐ super admin' : 'staff' }}
                </span>
                @if (auth()->user()->isSuperAdmin() && $user->id !== auth()->id())
                  <form method="POST" action="{{ route('admin.users.role', $user) }}" style="display:inline-flex;gap:6px;margin-top:6px;">
                    @csrf
                    @method('PUT')
                    <select name="role" style="padding:6px 10px;border-radius:10px;border:1px solid #E4D2A8;font-family:inherit;font-size:12.5px;">
                      <option value="staff" {{ $user->isSuperAdmin() ? '' : 'selected' }}>staff</option>
                      <option value="superadmin" {{ $user->isSuperAdmin() ? 'selected' : '' }}>super admin</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-line">Set</button>
                  </form>
                @endif
              </td>
              <td data-label="Added" class="muted">{{ $user->created_at->format('d M Y') }}</td>
              <td data-label="">
                @if ($user->id !== auth()->id())
                  <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                        onsubmit="return confirm('Remove staff account {{ $user->email }}?')" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                  </form>
                @else
                  <span class="muted">—</span>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <h3 style="font-size:16px;margin:20px 0 12px;">Add a new staff member</h3>
    <form method="POST" action="{{ route('admin.users.store') }}">
      @csrf
      <div class="form-grid">
        <div class="field">
          <label for="user_name">Name</label>
          <input type="text" id="user_name" name="name" required maxlength="100" placeholder="e.g. Wayan Barista">
        </div>
        <div class="field">
          <label for="user_email">E-mail</label>
          <input type="email" id="user_email" name="email" required placeholder="staff@kopirider.id">
        </div>
        <div class="field">
          <label for="user_password">Password</label>
          <input type="text" id="user_password" name="password" required minlength="8"
                 value="{{ old('user_password', substr(str_shuffle('abcdefghjkmnpqrstuvwxyz23456789'), 0, 10)) }}"
                 placeholder="Minimum 8 characters">
        </div>
        @if (auth()->user()->isSuperAdmin())
          <div class="field">
            <label for="user_role">Role</label>
            <select id="user_role" name="role">
              <option value="staff">Staff (no lead deletion)</option>
              <option value="superadmin">⭐ Super admin (can delete leads)</option>
            </select>
          </div>
        @endif
      </div>
      <p class="muted" style="margin:8px 0 12px;">A random password is pre-filled — copy it and share it with your staff member.</p>
      <button type="submit" class="btn">+ Add staff user</button>
    </form>
  </div>
@endsection
