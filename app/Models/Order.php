<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];

    protected $casts = [
        'attendees' => 'array',
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

    /** Attendees kept on the order until it is paid: [1 => ['name'=>, 'email'=>], ...] */
    public function attendeeList(): array
    {
        $list = [];
        foreach ((array) ($this->attendees ?? []) as $number => $person) {
            $list[(int) $number] = [
                'name' => (string) ($person['name'] ?? ''),
                'email' => (string) ($person['email'] ?? ''),
            ];
        }
        ksort($list);

        return $list;
    }

    /** The stored amounts, in the shape the email and thank-you page expect. */
    public function quoteSummary(): array
    {
        $setting = Setting::find(1);

        return [
            'unit' => (float) $this->unit_price,
            'is_early' => false,
            'quantity' => (int) $this->quantity,
            'subtotal' => round((float) $this->unit_price * $this->quantity, 2),
            'discount' => (float) $this->discount_total,
            'discount_label' => $this->discount_label,
            'net' => round((float) $this->total - (float) $this->tax_total, 2),
            'tax_percent' => (float) $this->tax_percent,
            'tax' => (float) $this->tax_total,
            'tax_included' => false,
            'tax_label' => (string) ($setting?->tax_label ?: 'Tax'),
            'total' => (float) $this->total,
            'per_pass' => $this->quantity ? round((float) $this->total / $this->quantity, 2) : 0.0,
        ];
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
