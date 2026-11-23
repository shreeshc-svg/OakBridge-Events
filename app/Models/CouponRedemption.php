<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A code used on an order. Its state always follows the order: awaiting payment =
 * reserved, paid = used, cancelled / failed / deleted = released (the code is free again).
 */
class CouponRedemption extends Model
{
    protected $guarded = [];

    protected $casts = ['discount' => 'decimal:2'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function code()
    {
        return $this->belongsTo(CouponCode::class, 'coupon_code_id');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    /** used | reserved | released */
    public function state(): string
    {
        return match ($this->order?->status) {
            'paid' => 'used',
            'pending' => 'reserved',
            default => 'released',
        };
    }
}
