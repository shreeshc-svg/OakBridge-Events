<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Ticketing > Coupons: a discount (e.g. "ALITA members – 20% off") with its codes. */
class Coupon extends Model
{
    protected $guarded = [];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'once_per_email' => 'boolean',
        'is_active' => 'boolean',
        'max_passes' => 'integer',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function passType()
    {
        return $this->belongsTo(PassType::class);
    }

    public function codes()
    {
        return $this->hasMany(CouponCode::class);
    }

    public function redemptions()
    {
        return $this->hasMany(CouponRedemption::class);
    }

    /** "20% off" / "₹2,000 off" */
    public function label(): string
    {
        return $this->discount_type === 'amount'
            ? \App\Support\Pricing::money((float) $this->discount_value) . ' off'
            : rtrim(rtrim(number_format((float) $this->discount_value, 2), '0'), '.') . '% off';
    }

    /** The discount on one pass of this price. */
    public function discountOn(float $unit): float
    {
        $off = $this->discount_type === 'amount'
            ? (float) $this->discount_value
            : $unit * (float) $this->discount_value / 100;

        return round(min(max($off, 0), $unit), 2);
    }

    /** Why the coupon cannot be used right now, or null. */
    public function closedReason(): ?string
    {
        if (! $this->is_active) {
            return 'This code is no longer active.';
        }
        if ($this->valid_from && $this->valid_from->isFuture()) {
            return 'This code can be used from ' . $this->valid_from->format('j F Y') . '.';
        }
        if ($this->valid_until && $this->valid_until->copy()->endOfDay()->isPast()) {
            return 'This code expired on ' . $this->valid_until->format('j F Y') . '.';
        }

        return null;
    }
}
