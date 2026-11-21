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
        'min_per_person' => 'integer',
        'open_to_anyone' => 'boolean',
        'views' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (SpecialOffer $offer) {
            $offer->code = $offer->code ?: \Illuminate\Support\Str::random(10);
        });
    }

    /** The one link for a mail campaign: /offer/o/{code}. */
    public function sharedLink(): string
    {
        return route('offer.shared', $this->code);
    }

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

    /** The least passes per checkout (never more than the most). */
    public function minPasses(): int
    {
        return max(1, min((int) ($this->min_per_person ?: 1), $this->maxPasses()));
    }

    public function maxPasses(): int
    {
        return max(1, (int) $this->max_per_person);
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
