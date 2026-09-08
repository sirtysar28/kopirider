<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'lead_id', 'type', 'amount', 'currency', 'method',
        'payment_link', 'midtrans_order_id', 'status', 'paid_at', 'note',
    ];

    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
            'amount' => 'decimal:2',
        ];
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function getFormattedAmountAttribute(): string
    {
        $symbol = $this->currency === 'IDR' ? 'Rp' : $this->currency.' ';

        return $symbol.number_format((float) $this->amount, $this->currency === 'IDR' ? 0 : 2);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'paid' => 'paid',
            'failed' => 'failed',
            'expired' => 'expired',
            default => 'pending',
        };
    }
}
