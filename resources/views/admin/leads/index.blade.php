@extends('layouts.admin')

@section('title', 'Leads')

@section('content')
<div class="page-head">
  <div>
    <h1>Leads</h1>
    <p class="muted">Completed and partial enquiries — partials are worth following up.</p>
  </div>
</div>

<div class="card">
  <form method="GET" class="filters">
    <input type="text" name="search" placeholder="Search name, WhatsApp or reference…" value="{{ $filters['search'] ?? '' }}">
    <select name="status">
      <option value="">All statuses</option>
      @foreach (['new', 'contacted', 'negotiating', 'confirmed', 'lost'] as $st)
        <option value="{{ $st }}" {{ ($filters['status'] ?? '') === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
      @endforeach
    </select>
    <select name="complete">
      <option value="">Complete &amp; partial</option>
      <option value="1" {{ ($filters['complete'] ?? '') === '1' ? 'selected' : '' }}>Complete only</option>
      <option value="0" {{ ($filters['complete'] ?? '') === '0' ? 'selected' : '' }}>Partial only</option>
    </select>
    <button type="submit" class="btn btn-sm">Filter</button>
    <a href="{{ route('admin.leads.index') }}" class="btn btn-sm btn-line">Reset</a>
  </form>

  <div class="table-wrap table-cards">
    <table class="list">
      <thead>
        <tr>
          <th>Ref</th><th>Name</th><th>WhatsApp</th><th>Event</th><th>Date</th>
          <th>Guests</th><th>Package</th><th>Status</th><th>Type</th><th>WA opened</th><th>Created</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($leads as $lead)
          <tr>
            <td data-label="Ref"><a href="{{ route('admin.leads.show', $lead) }}" style="color:#8B4226;font-weight:700;">{{ $lead->reference }}</a></td>
            <td data-label="Name">{{ $lead->name ?? '—' }}</td>
            <td data-label="WhatsApp">{{ $lead->whatsapp ?? '—' }}</td>
            <td data-label="Event">{{ ucfirst($lead->event_type ?? '?') }}</td>
            <td data-label="Date">{{ $lead->event_date_formatted }}</td>
            <td data-label="Guests">{{ $lead->guest_range ?? '—' }}</td>
            <td data-label="Package">{{ $lead->package?->name ?? ($lead->event_type && in_array($lead->event_type, ['market','festival','community']) ? 'Free option' : '—') }}</td>
            <td data-label="Status"><span class="badge {{ $lead->status }}">{{ $lead->status }}</span></td>
            <td data-label="Type"><span class="badge {{ $lead->is_complete ? 'complete' : 'partial' }}">{{ $lead->is_complete ? 'complete' : 'partial' }}</span></td>
            <td data-label="WA opened">{{ $lead->whatsapp_opened_at?->format('d M H:i') ?? '—' }}</td>
            <td data-label="Created" class="muted">{{ $lead->created_at->format('d M Y H:i') }}</td>
          </tr>
        @empty
          <tr><td colspan="11" class="muted">No leads match the filters.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{ $leads->links('partials.admin-pagination') }}
</div>
@endsection
