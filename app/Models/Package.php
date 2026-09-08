<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'starting_price', 'currency',
        'features', 'audience', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'is_active' => 'boolean',
            'starting_price' => 'decimal:2',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function scopePaid($query)
    {
        return $query->where('audience', 'paid');
    }

    public function getFeaturesListAttribute(): array
    {
        return $this->features ?? [];
    }

    public function getFormattedPriceAttribute(): string
    {
        if ($this->audience === 'free') {
            return 'No upfront fee';
        }

        $symbol = $this->currency === 'IDR' ? 'Rp ' : $this->currency.' ';

        $amount = $this->currency === 'IDR'
            ? number_format((float) $this->starting_price, 0, ',', '.')
            : number_format((float) $this->starting_price, 0);

        return $symbol.$amount;
    }
}
