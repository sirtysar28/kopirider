<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsEvent;
use App\Models\DateStatus;
use App\Models\Lead;
use App\Models\Payment;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'leads_total' => Lead::count(),
            'leads_new' => Lead::where('status', 'new')->where('is_complete', true)->count(),
            'leads_partial' => Lead::where('is_complete', false)->count(),
            'leads_confirmed' => Lead::where('status', 'confirmed')->count(),
            'revenue_paid' => (float) Payment::where('status', 'paid')->sum('amount'),
            'payments_pending' => Payment::where('status', 'pending')->count(),
            'booked_days' => DateStatus::where('status', 'booked')->count(),
            'enquiry_days' => DateStatus::where('status', 'enquiry')->count(),
        ];

        $recentLeads = Lead::with('package')
            ->latest('id')
            ->limit(8)
            ->get();

        $upcoming = Lead::query()
            ->where('is_complete', true)
            ->whereDate('event_date', '>=', now())
            ->orderBy('event_date')
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentLeads', 'upcoming'));
    }
}
