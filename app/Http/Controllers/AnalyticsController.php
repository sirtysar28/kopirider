<?php

namespace App\Http\Controllers;

use App\Models\AnalyticsEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    private const ALLOWED = AnalyticsEvent::FUNNEL_STEPS;

    public function track(Request $request): JsonResponse
    {
        $data = $request->validate([
            'event_name' => ['required', 'string', 'max:50'],
            'session_id' => ['required', 'string', 'max:64'],
            'meta' => ['nullable', 'array'],
        ]);

        if (! in_array($data['event_name'], self::ALLOWED, true)) {
            return response()->json(['ok' => false], 422);
        }

        AnalyticsEvent::create([
            'event_name' => $data['event_name'],
            'session_id' => $data['session_id'],
            'meta' => $data['meta'] ?? null,
            'created_at' => now(),
        ]);

        return response()->json(['ok' => true]);
    }
}
