<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'reference', 'session_id', 'event_date', 'event_type', 'guest_range',
        'package_id', 'name', 'whatsapp', 'message', 'source', 'status',
        'is_complete', 'whatsapp_opened_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date:Y-m-d',
            'is_complete' => 'boolean',
            'whatsapp_opened_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public static function nextReference(): string
    {
        $last = static::where('reference', 'like', 'KR-%')
            ->orderByDesc('id')
            ->value('reference');

        $number = $last ? (int) substr($last, 3) + 1 : 1;

        return 'KR-'.str_pad((string) $number, 4, '0', STR_PAD_LEFT);
    }

    public function getEventDateFormattedAttribute(): string
    {
        return $this->event_date?->format('j F Y') ?? '—';
    }

    public function getDepositPaymentAttribute(): ?Payment
    {
        return $this->payments->firstWhere('type', 'deposit');
    }

    public function getBalancePaymentAttribute(): ?Payment
    {
        return $this->payments->firstWhere('type', 'balance');
    }
}
