<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsEvent;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $days = min(90, max(1, (int) $request->input('days', 30)));
        $since = now()->subDays($days);

        $steps = collect(AnalyticsEvent::FUNNEL_STEPS)
            ->mapWithKeys(function ($step) use ($since) {
                return [$step => AnalyticsEvent::where('event_name', $step)
                    ->where('created_at', '>=', $since)
                    ->distinct('session_id')
                    ->count()];
            });

        $top = $steps->get('booking_flow_opened', 0);

        $daily = AnalyticsEvent::query()
            ->where('event_name', 'lead_submitted')
            ->where('created_at', '>=', $since)
            ->selectRaw('DATE(created_at) as d, COUNT(DISTINCT session_id) as c')
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('c', 'd');

        return view('admin.analytics.index', compact('steps', 'top', 'daily', 'days'));
    }
}
