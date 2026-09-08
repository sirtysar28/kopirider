@extends('layouts.admin')

@section('title', 'Lead '.$lead->reference)

@section('content')
<div class="breadcrumb">
  <a href="{{ route('admin.leads.index') }}">← All leads</a>
</div>
<div class="page-head">
  <div>
    <h1>{{ $lead->reference }} {{ $lead->name ? '· '.$lead->name : '' }}</h1>
    <p class="muted">
      Created {{ $lead->created_at->format('d F Y, H:i') }} ·
      <span class="badge {{ $lead->is_complete ? 'complete' : 'partial' }}">{{ $lead->is_complete ? 'complete' : 'partial' }}</span>
    </p>
  </div>
  @if ($lead->whatsapp)
    <div class="actions">
      <a class="wa-fab" href="{{ wa_link($lead->whatsapp, $lead->message ?: 'Hi '.$lead->name.'! Thanks for your enquiry about the coffee truck.') }}" target="_blank" rel="noopener">
        💬 Open WhatsApp chat
      </a>
    </div>
  @endif
</div>

<div class="card">
  <h2 style="font-size:19px;margin-bottom:14px;">Event details</h2>
  <div class="detail-grid">
    <div class="item"><div class="k">Event date</div><div class="v">{{ $lead->event_date_formatted }}</div></div>
    <div class="item"><div class="k">Event type</div><div class="v">{{ ucfirst($lead->event_type ?? '—') }}</div></div>
    <div class="item"><div class="k">Guest range</div><div class="v">{{ $lead->guest_range ?? '—' }}</div></div>
    <div class="item">
      <div class="k">Package</div>
      <div class="v">
        @if ($lead->package)
          {{ $lead->package->name }} (from {{ $lead->package->formatted_price }})
        @elseif ($lead->event_type && in_array($lead->event_type, ['market', 'festival', 'community']))
          Free event option
        @else
          —
        @endif
      </div>
    </div>
    <div class="item"><div class="k">WhatsApp</div><div class="v">{{ $lead->whatsapp ?? '—' }}</div></div>
    <div class="item"><div class="k">WA opened at</div><div class="v">{{ $lead->whatsapp_opened_at?->format('d M Y H:i') ?? 'Never' }}</div></div>
  </div>

  @if ($lead->message)
    <div style="margin-top:16px;background:#FBF6EA;border-left:4px solid #8B4226;border-radius:12px;padding:14px 18px;font-size:14px;">
      <strong>Pre-written message:</strong><br>{{ $lead->message }}
    </div>
  @endif
</div>

<div class="card">
  <h2 style="font-size:19px;margin-bottom:14px;">Update lead</h2>
  <form method="POST" action="{{ route('admin.leads.update', $lead) }}">
    @csrf
    @method('PUT')
    <div class="form-grid">
      <div class="field">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="{{ old('name', $lead->name) }}">
      </div>
      <div class="field">
        <label for="whatsapp">WhatsApp</label>
        <input type="text" id="whatsapp" name="whatsapp" value="{{ old('whatsapp', $lead->whatsapp) }}">
      </div>
      <div class="field">
        <label for="status">Status</label>
        <select id="status" name="status">
          @foreach (['new', 'contacted', 'negotiating', 'confirmed', 'lost'] as $st)
            <option value="{{ $st }}" {{ old('status', $lead->status) === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <p class="muted" style="margin:10px 0 14px;">
      Marking a lead <strong>confirmed</strong> reserves the date — it will show as "someone is asking"
      on the public calendar until you set it to <strong>booked</strong> in the Calendar page.
    </p>
    <button type="submit" class="btn">Save changes</button>
  </form>
</div>

<div class="card">
  <h2 style="font-size:19px;margin-bottom:6px;">Payments</h2>
  <p class="muted" style="margin-bottom:14px;">
    Deposit first, balance later.
    @if (\App\Services\MidtransService::enabled())
      ✅ Midtrans is <strong>enabled</strong> — generate a payment link per record and send it via WhatsApp.
    @else
      ⚠️ Midtrans is <strong>disabled</strong> — records default to bank transfer. Enable it in Settings → Payments.
    @endif
  </p>

  @if (setting('bank_transfer_details'))
    <div style="background:#FBF6EA;border:1px dashed #E4D2A8;border-radius:14px;padding:12px 16px;margin-bottom:16px;font-size:13.5px;white-space:pre-line;">{{ setting('bank_transfer_details') }}</div>
  @endif

  @if ($lead->payments->isNotEmpty())
    <div class="table-wrap" style="margin-bottom:18px;">
      <table class="list">
        <thead>
          <tr><th>Type</th><th>Amount</th><th>Method</th><th>Link</th><th>Status</th><th>Paid at</th><th></th></tr>
        </thead>
        <tbody>
          @foreach ($lead->payments as $payment)
            <tr>
              <td style="font-weight:700;capitalize;">{{ ucfirst($payment->type) }}</td>
              <td>{{ $payment->formatted_amount }}</td>
              <td>{{ str_replace('_', ' ', $payment->method) }}</td>
              <td>
                @if ($payment->payment_link)
                  <a href="{{ $payment->payment_link }}" target="_blank" rel="noopener" style="color:#8B4226;font-weight:600;">Open link ↗</a>
                @else
                  —
                @endif
              </td>
              <td><span class="badge {{ $payment->status_badge }}">{{ $payment->status }}</span></td>
              <td class="muted">{{ $payment->paid_at?->format('d M Y H:i') ?? '—' }}</td>
              <td>
                <div style="display:flex;gap:8px;align-items:center;white-space:nowrap;">
                  @if ($payment->method === 'midtrans' && $payment->status === 'pending' && ! $payment->payment_link && \App\Services\MidtransService::enabled())
                    <form method="POST" action="{{ route('admin.payments.generate-link', $payment) }}">
                      @csrf
                      <button class="btn btn-sm">⚡ Generate Midtrans link</button>
                    </form>
                  @endif
                  <form method="POST" action="{{ route('admin.payments.destroy', $payment) }}" onsubmit="return confirm('Delete this payment record?')" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger">Delete</button>
                  </form>
                </div>
              </td>
            </tr>
            <tr>
              <td colspan="7" style="padding-top:0;">
                <form method="POST" action="{{ route('admin.payments.update', $payment) }}" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
                  @csrf
                  @method('PUT')
                  <select name="status" style="padding:8px 12px;border-radius:10px;border:1px solid #E4D2A8;font-family:inherit;">
                    @foreach (['pending', 'paid', 'failed', 'expired'] as $st)
                      <option value="{{ $st }}" {{ $payment->status === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                    @endforeach
                  </select>
                  <input type="url" name="payment_link" value="{{ $payment->payment_link }}" placeholder="Paste Midtrans payment link…" style="flex:1;min-width:220px;padding:8px 12px;border-radius:10px;border:1px solid #E4D2A8;font-family:inherit;">
                  <button class="btn btn-sm">Update</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.payments.store', $lead) }}">
    @csrf
    <div class="form-grid">
      <div class="field">
        <label for="ptype">Type</label>
        <select id="ptype" name="type">
          <option value="deposit">Deposit</option>
          <option value="balance">Balance</option>
          <option value="full">Full payment</option>
        </select>
      </div>
      <div class="field">
        <label for="pamount">Amount ({{ setting('currency', 'IDR') }})</label>
        <input type="number" id="pamount" name="amount" min="0" step="1000" required placeholder="e.g. 2500000">
      </div>
      <div class="field">
        <label for="pmethod">Method</label>
        <select id="pmethod" name="method">
          @if (\App\Services\MidtransService::enabled())
            <option value="midtrans">Midtrans payment link</option>
          @endif
          <option value="bank_transfer" {{ \App\Services\MidtransService::enabled() ? '' : 'selected' }}>Bank transfer</option>
        </select>
      </div>
      <div class="field">
        <label for="plink">Payment link (optional)</label>
        <input type="url" id="plink" name="payment_link" placeholder="https://midtrans link…">
      </div>
    </div>
    <button type="submit" class="btn" style="margin-top:14px;">+ Add payment request</button>
  </form>
</div>
@endsection
