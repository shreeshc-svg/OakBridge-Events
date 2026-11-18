<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Ticketing > Special price offers: one pass at one price for a list of invited people. */
class SpecialOffer extends Model
{
    protected $guarded = [];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'charge_tax' => 'boolean',
        'is_active' => 'boolean',
        'expires_on' => 'date',
        'max_per_person' => 'integer',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function passType()
    {
        return $this->belongsTo(PassType::class);
    }

    public function invites()
    {
        return $this->hasMany(OfferInvite::class);
    }

    /** Why the offer links do not work right now, or null when they do. */
    public function closedReason(): ?string
    {
        if (! $this->is_active) {
            return 'This offer has been closed.';
        }
        if ($this->expires_on && $this->expires_on->endOfDay()->isPast()) {
            return 'This offer ended on ' . $this->expires_on->format('j F Y') . '.';
        }
        if (! $this->service || ! $this->service->date || $this->service->date->lt(today())) {
            return 'This event is over.';
        }
        if (! $this->passType || ! $this->passType->is_active || (int) $this->passType->service_id !== (int) $this->service_id) {
            return 'This offer is not available at the moment.';
        }

        return null;
    }

    public function isOpen(): bool
    {
        return $this->closedReason() === null;
    }
}
