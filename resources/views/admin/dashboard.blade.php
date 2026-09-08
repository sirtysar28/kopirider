@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="page-head">
  <div>
    <h1>Dashboard</h1>
    <p class="muted">Snapshot of bookings, leads and payments.</p>
  </div>
</div>

<div class="stats-grid">
  <div class="stat"><div class="num">{{ $stats['leads_total'] }}</div><div class="lbl">Total leads</div></div>
  <div class="stat"><div class="num">{{ $stats['leads_new'] }}</div><div class="lbl">New enquiries</div></div>
  <div class="stat"><div class="num">{{ $stats['leads_partial'] }}</div><div class="lbl">Partial (follow up)</div></div>
  <div class="stat"><div class="num">{{ $stats['leads_confirmed'] }}</div><div class="lbl">Confirmed</div></div>
  <div class="stat"><div class="num">Rp {{ number_format($stats['revenue_paid'], 0, ',', '.') }}</div><div class="lbl">Paid revenue</div></div>
  <div class="stat"><div class="num">{{ $stats['payments_pending'] }}</div><div class="lbl">Pending payments</div></div>
  <div class="stat"><div class="num">{{ $stats['booked_days'] }}</div><div class="lbl">Booked days</div></div>
  <div class="stat"><div class="num">{{ $stats['enquiry_days'] }}</div><div class="lbl">Enquiry days</div></div>
</div>

<div class="card">
  <div class="page-head" style="margin-bottom:12px;">
    <h2 style="font-size:20px;">Recent leads</h2>
    <a href="{{ route('admin.leads.index') }}" class="btn btn-sm btn-line">View all →</a>
  </div>
  <div class="table-wrap table-cards">
    <table class="list">
      <thead>
        <tr>
          <th>Ref</th><th>Name</th><th>Event</th><th>Date</th><th>Guests</th><th>Status</th><th>Complete</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($recentLeads as $lead)
          <tr>
            <td data-label="Ref"><a href="{{ route('admin.leads.show', $lead) }}" style="color:#8B4226;font-weight:700;">{{ $lead->reference }}</a></td>
            <td data-label="Name">{{ $lead->name ?? '—' }}</td>
            <td data-label="Event">{{ ucfirst($lead->event_type ?? '?') }}</td>
            <td data-label="Date">{{ $lead->event_date_formatted }}</td>
            <td data-label="Guests">{{ $lead->guest_range ?? '—' }}</td>
            <td data-label="Status"><span class="badge {{ $lead->status }}">{{ $lead->status }}</span></td>
            <td data-label="Complete">
              <span class="badge {{ $lead->is_complete ? 'complete' : 'partial' }}">
                {{ $lead->is_complete ? 'complete' : 'partial' }}
              </span>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="muted">No leads yet — share the /check-date link to get started.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="card">
  <h2 style="font-size:20px;margin-bottom:12px;">Upcoming events</h2>
  <div class="table-wrap table-cards">
    <table class="list">
      <thead>
        <tr><th>Ref</th><th>Name</th><th>Event date</th><th>Type</th><th>Guests</th><th>Status</th></tr>
      </thead>
      <tbody>
        @forelse ($upcoming as $lead)
          <tr>
            <td data-label="Ref"><a href="{{ route('admin.leads.show', $lead) }}" style="color:#8B4226;font-weight:700;">{{ $lead->reference }}</a></td>
            <td data-label="Name">{{ $lead->name ?? '—' }}</td>
            <td data-label="Event date">{{ $lead->event_date_formatted }}</td>
            <td data-label="Type">{{ ucfirst($lead->event_type ?? '?') }}</td>
            <td data-label="Guests">{{ $lead->guest_range ?? '—' }}</td>
            <td data-label="Status"><span class="badge {{ $lead->status }}">{{ $lead->status }}</span></td>
          </tr>
        @empty
          <tr><td colspan="6" class="muted">No confirmed upcoming events yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
