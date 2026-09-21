<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PassType extends Model
{
    protected $guarded = [];

    protected $casts = [
        'price' => 'decimal:2',
        'early_price' => 'decimal:2',
        'early_until' => 'date',
        'is_active' => 'boolean',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /** Early-bird price while the offer is on, otherwise the normal price. */
    public function priceOn(?\Carbon\Carbon $date = null): float
    {
        $date = $date ?: now();
        if ($this->early_price !== null && $this->early_until && $date->startOfDay()->lte($this->early_until)) {
            return (float) $this->early_price;
        }

        return (float) $this->price;
    }

    public function isEarlyOn(?\Carbon\Carbon $date = null): bool
    {
        return $this->early_price !== null && $this->early_until
            && ($date ?: now())->startOfDay()->lte($this->early_until);
    }
}
