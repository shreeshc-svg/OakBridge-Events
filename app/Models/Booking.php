<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'complimentary_at' => 'datetime',
        'emailed_at' => 'datetime',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    protected static function booted(): void
    {
        // safety net: a pass deleted anywhere else still lands in the cancelled-passes log
        static::deleting(function (Booking $booking) {
            \App\Support\CancelledPasses::record([$booking], 'Deleted');
        });
    }

    /** A free pass issued from Admin > Ticketing > Complimentary passes. */
    public function isComplimentary(): bool
    {
        return $this->complimentary_at !== null;
    }
}