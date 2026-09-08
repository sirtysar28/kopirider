@extends('layouts.admin')

@section('title', 'Analytics')

@section('content')
<div class="page-head">
  <div>
    <h1>Analytics — booking funnel</h1>
    <p class="muted">Where visitors drop off between opening the flow and opening WhatsApp.</p>
  </div>
  <div class="actions">
    <a class="btn btn-sm {{ $days === 7 ? '' : 'btn-line' }}" href="{{ route('admin.analytics.index', ['days' => 7]) }}">7 days</a>
    <a class="btn btn-sm {{ $days === 30 ? '' : 'btn-line' }}" href="{{ route('admin.analytics.index', ['days' => 30]) }}">30 days</a>
    <a class="btn btn-sm {{ $days === 90 ? '' : 'btn-line' }}" href="{{ route('admin.analytics.index', ['days' => 90]) }}">90 days</a>
  </div>
</div>

<div class="card">
  @php $max = max(1, $steps->max()) @endphp
  @foreach ($steps as $step => $count)
    <div class="funnel-step">
      <div class="name">{{ str_replace('_', ' ', $step) }}</div>
      <div class="bar-wrap">
        <div class="bar" style="width: {{ round($count / $max * 100) }}%;"></div>
      </div>
      <div class="val">
        {{ $count }}{{ $top > 0 ? ' · '.round($count / max(1, $steps->get('booking_flow_opened')) * 100).'%' : '' }}
      </div>
    </div>
  @endforeach
</div>

<div class="card">
  <h2 style="font-size:19px;margin-bottom:14px;">Leads submitted per day</h2>
  @if ($daily->isEmpty())
    <p class="muted">No submissions in this period yet.</p>
  @else
    <div class="table-wrap">
      <table class="list" style="min-width:320px;">
        <thead><tr><th>Date</th><th>Distinct sessions submitting</th></tr></thead>
        <tbody>
          @foreach ($daily as $day => $count)
            <tr>
              <td>{{ \Carbon\Carbon::parse($day)->format('l, d F Y') }}</td>
              <td style="font-weight:700;">{{ $count }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>
@endsection
