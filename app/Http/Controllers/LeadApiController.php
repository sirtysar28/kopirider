<?php

namespace App\Http\Controllers;

use App\Models\AnalyticsEvent;
use App\Models\DateStatus;
use App\Models\Lead;
use App\Models\Package;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LeadApiController extends Controller
{
    public const EVENT_TYPES = [
        'wedding', 'private', 'corporate', 'market', 'festival', 'community', 'other',
    ];

    public const GUEST_RANGES = ['<50', '50-100', '100-200', '200-300', '300-500', '500+'];

    /**
     * Upsert a lead (partial or complete) and return the WhatsApp
     * redirect URL only after the row is committed to the database.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'session_id' => ['required', 'string', 'max:64'],
            'event_date' => ['nullable', 'date', 'after_or_equal:today'],
            'event_type' => ['nullable', 'string', 'in:'.implode(',', self::EVENT_TYPES)],
            'guest_range' => ['nullable', 'string', 'in:'.implode(',', self::GUEST_RANGES)],
            'package_id' => ['nullable', 'integer', 'exists:packages,id'],
            'name' => ['nullable', 'string', 'max:120'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'complete' => ['nullable', 'boolean'],
        ]);

        $isComplete = (bool) ($data['complete'] ?? false)
            && ! empty($data['name'])
            && ! empty($data['whatsapp']);

        // ---- Validation for a complete submission ----
        if ($isComplete) {
            if (empty($data['event_date']) || empty($data['event_type']) || empty($data['guest_range'])) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Please complete every step of the booking flow first.',
                ], 422);
            }
        }

        // Reuse the row belonging to this browser session so partial
        // answers progressively enrich the same lead.
        $lead = Lead::query()
            ->where('session_id', $data['session_id'])
            ->where('is_complete', false)
            ->latest('id')
            ->first();

        $attributes = [
            'event_date' => $data['event_date'] ?? $lead?->event_date,
            'event_type' => $data['event_type'] ?? $lead?->event_type,
            'guest_range' => $data['guest_range'] ?? $lead?->guest_range,
            'package_id' => $data['package_id'] ?? $lead?->package_id,
            'name' => $data['name'] ?? null,
            'whatsapp' => $data['whatsapp'] ?? null,
        ];

        // Never confirm a booking automatically — only mark as "enquiry".
        if ($isComplete) {
            $attributes['message'] = $this->buildMessage($attributes);
            $attributes['is_complete'] = true;
            $attributes['completed_at'] = now();
            $attributes['status'] = 'new';
        }

        if ($lead) {
            $lead->fill(array_filter($attributes, fn ($v) => $v !== null))->save();
        } else {
            $attributes['reference'] = Lead::nextReference();
            $attributes['session_id'] = $data['session_id'];
            $attributes['source'] = $request->headers->get('referer') ? 'website' : 'direct';
            $lead = Lead::create($attributes);
        }

        // Mark the requested date as "enquiry" on the calendar so the
        // next visitor sees it as yellow while the team reviews it.
        if ($lead->event_date && $isComplete) {
            DateStatus::firstOrCreate(
                ['date' => $lead->event_date->format('Y-m-d')],
                ['status' => 'enquiry', 'note' => 'Lead '.$lead->reference],
            );
        }

        // Notify the team (log channel for now; easy to swap for
        // WhatsApp Business API notifications later).
        if ($isComplete) {
            Log::channel('stack')->info('New complete lead saved: '.$lead->reference, [
                'name' => $lead->name,
                'event_date' => $lead->event_date?->format('Y-m-d'),
                'event_type' => $lead->event_type,
            ]);
        }

        return response()->json([
            'ok' => true,
            'saved' => true,          // the DB write is finished before we hand back the URL
            'reference' => $lead->reference,
            'complete' => $lead->is_complete,
            'whatsapp_url' => $isComplete ? wa_link(setting('whatsapp_number'), $lead->message) : null,
        ]);
    }

    /**
     * Mark the moment WhatsApp was opened for a lead.
     */
    public function whatsappOpened(Request $request): JsonResponse
    {
        $data = $request->validate([
            'session_id' => ['required', 'string'],
        ]);

        Lead::query()
            ->where('session_id', $data['session_id'])
            ->where('is_complete', true)
            ->latest('id')
            ->first()
            ?->update(['whatsapp_opened_at' => now()]);

        return response()->json(['ok' => true]);
    }

    private function buildMessage(array $a): string
    {
        $date = \Illuminate\Support\Carbon::parse($a['event_date'])->format('j F Y');
        $type = str_replace('_', ' ', $a['event_type'] ?? 'an event');

        return "Hi! I'd like to book the truck for {$date}, a {$type}, around {$a['guest_range']} guests.";
    }
}
