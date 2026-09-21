<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public const STATUSES = [
        'pending' => 'Payment pending',
        'paid' => 'Paid',
        'failed' => 'Failed',
        'cancelled' => 'Cancelled',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
