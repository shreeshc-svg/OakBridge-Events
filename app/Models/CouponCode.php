<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** One code of a coupon: a shared code, or a one-time code for one person. */
class CouponCode extends Model
{
    protected $guarded = [];

    protected $casts = [
        'lock_to_email' => 'boolean',
        'is_active' => 'boolean',
        'max_uses' => 'integer',
    ];

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function redemptions()
    {
        return $this->hasMany(CouponRedemption::class);
    }

    /** Uses that count: their order is awaiting payment or paid. */
    public function activeRedemptions()
    {
        return $this->redemptions()->whereHas('order', fn ($q) => $q->whereIn('status', ['pending', 'paid']));
    }
}
