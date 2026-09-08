<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsEvent extends Model
{
    public $timestamps = false;

    protected $fillable = ['event_name', 'session_id', 'meta', 'created_at'];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public const FUNNEL_STEPS = [
        'booking_flow_opened',
        'date_selected',
        'event_type_selected',
        'guest_range_selected',
        'package_viewed',
        'package_selected',
        'contact_screen_viewed',
        'lead_submitted',
        'whatsapp_opened',
    ];
}
