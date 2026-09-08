<?php

namespace App\Http\Controllers;

use App\Models\DateStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CalendarApiController extends Controller
{
    public function month(Request $request): JsonResponse
    {
        $data = $request->validate([
            'month' => ['nullable', 'date_format:Y-m'],
        ]);

        $month = $data['month'] ?? now()->format('Y-m');
        [$year, $mon] = array_map('intval', explode('-', $month));

        $start = sprintf('%04d-%02d-01', $year, $mon);
        $end = sprintf('%04d-%02d-%02d', $year, $mon, cal_days_in_month(CAL_GREGORIAN, $mon, $year));

        // Default: any lead with an event date in range marks the date as "enquiry"
        $leads = \App\Models\Lead::query()
            ->whereNotNull('event_date')
            ->whereBetween('event_date', [$start, $end])
            ->get();

        $override = DateStatus::whereBetween('date', [$start, $end])->get()->keyBy(
            fn ($d) => $d->date->format('Y-m-d')
        );

        $days = [];

        foreach ($override as $date => $row) {
            $days[$date] = $row->status;
        }

        // Leads create/upgrade enquiry status unless an explicit override exists.
        // AVAILABLE → enquiry (lead) → booked (confirmed by the team).
        foreach ($leads as $lead) {
            $date = $lead->event_date->format('Y-m-d');
            $current = $days[$date] ?? null;

            if ($current === 'booked') {
                continue; // explicit "booked" always wins
            }

            $days[$date] = $lead->status === 'confirmed' ? 'booked' : 'enquiry';
        }

        return response()->json([
            'month' => $month,
            'days' => $days,
            'legend' => [
                'available' => 'Available',
                'enquiry' => 'Someone is asking',
                'booked' => 'Booked',
            ],
        ]);
    }
}
