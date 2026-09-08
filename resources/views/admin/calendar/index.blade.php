@extends('layouts.admin')

@section('title', 'Booking calendar')

@section('content')
<div class="page-head">
  <div>
    <h1>Booking calendar</h1>
    <p class="muted">
      🟢 Available · 🟡 Someone is asking · 🔴 Booked. Complete leads automatically turn
      their date yellow; set a date to booked once the deposit is paid.
    </p>
  </div>
  <div class="actions" style="gap:6px;">
    @php
      $prev = \Carbon\Carbon::create($year, $mon, 1)->subMonth();
      $next = \Carbon\Carbon::create($year, $mon, 1)->addMonth();
    @endphp
    <a class="btn btn-sm btn-line" href="{{ route('admin.calendar.index', ['month' => $prev->format('Y-m')]) }}">← {{ $prev->translatedFormat('M Y') }}</a>
    <span style="font-family:'Fraunces',serif;font-size:19px;font-weight:700;padding:0 10px;">
      {{ \Carbon\Carbon::create($year, $mon, 1)->translatedFormat('F Y') }}
    </span>
    <a class="btn btn-sm btn-line" href="{{ route('admin.calendar.index', ['month' => $next->format('Y-m')]) }}">{{ $next->translatedFormat('M Y') }} →</a>
  </div>
</div>

<div class="card">
  <div class="table-wrap">
    <div class="admin-cal">
    @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $d)
      <div class="head">{{ $d }}</div>
    @endforeach

    @php
      $first = \Carbon\Carbon::create($year, $mon, 1);
      $offset = ($first->dayOfWeekIso - 1);
    @endphp
    @for ($i = 0; $i < $offset; $i++)
      <div class="day-cell empty"></div>
    @endfor

    @for ($d = 1; $d <= $daysInMonth; $d++)
      @php
        $dateStr = sprintf('%04d-%02d-%02d', $year, $mon, $d);
        $status = $statuses[$dateStr]->status ?? 'available';
        $note = $statuses[$dateStr]->note ?? null;
      @endphp
      <div class="day-cell {{ $status }}">
        <div class="dnum">{{ $d }}</div>
        <div class="day-status">{{ $status }}</div>
        <form method="POST" action="{{ route('admin.calendar.update') }}">
          @csrf
          <input type="hidden" name="date" value="{{ $dateStr }}">
          <select name="status" onchange="this.form.note.value=this.form.note.value; this.form.submit()">
            <option value="available" {{ $status === 'available' ? 'selected' : '' }}>🟢 available</option>
            <option value="enquiry" {{ $status === 'enquiry' ? 'selected' : '' }}>🟡 enquiry</option>
            <option value="booked" {{ $status === 'booked' ? 'selected' : '' }}>🔴 booked</option>
          </select>
          <input type="text" name="note" value="{{ $note }}" placeholder="note (optional)">
        </form>
      </div>
    @endfor
    </div>
  </div>
  <p class="muted" style="margin-top:14px;">
    Changing the status saves immediately. The public calendar reflects it right away.
  </p>
</div>
@endsection
