<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DateStatus;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));

        if (! preg_match('/^\d{4}-\d{2}$/', $month)) {
            $month = now()->format('Y-m');
        }

        [$year, $mon] = array_map('intval', explode('-', $month));
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $mon, $year);

        $statuses = DateStatus::query()
            ->whereBetween('date', [
                sprintf('%04d-%02d-01', $year, $mon),
                sprintf('%04d-%02d-%02d', $year, $mon, $daysInMonth),
            ])
            ->get()
            ->keyBy(fn ($d) => $d->date->format('Y-m-d'));

        return view('admin.calendar.index', [
            'month' => $month,
            'year' => $year,
            'mon' => $mon,
            'daysInMonth' => $daysInMonth,
            'statuses' => $statuses,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'status' => ['required', 'in:available,enquiry,booked'],
            'note' => ['nullable', 'string', 'max:200'],
        ]);

        if ($data['status'] === 'available') {
            DateStatus::whereDate('date', $data['date'])->delete();
        } else {
            DateStatus::updateOrCreate(
                ['date' => $data['date']],
                ['status' => $data['status'], 'note' => $data['note'] ?? null],
            );
        }

        return back()->with('success', 'Calendar updated for '.$data['date'].'.');
    }
}
