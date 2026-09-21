<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingTier extends Model
{
    protected $guarded = [];

    protected $casts = [
        'min_quantity' => 'integer',
        'discount_value' => 'decimal:2',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /** Money off one pass at this slab. */
    public function discountPerPass(float $unitPrice): float
    {
        $off = $this->discount_type === 'percent'
            ? $unitPrice * ((float) $this->discount_value / 100)
            : (float) $this->discount_value;

        return round(min(max($off, 0), $unitPrice), 2);
    }

    public function label(): string
    {
        $off = $this->discount_type === 'percent'
            ? rtrim(rtrim(number_format((float) $this->discount_value, 2), '0'), '.') . '% off'
            : '₹' . number_format((float) $this->discount_value) . ' off per pass';

        return $this->min_quantity . '+ passes – ' . $off;
    }
}
