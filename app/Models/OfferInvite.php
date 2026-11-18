<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** One person on a special offer's list, with their personal link (/offer/{token}). */
class OfferInvite extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_test' => 'boolean',
        'clicked_at' => 'datetime',
    ];

    public function offer()
    {
        return $this->belongsTo(SpecialOffer::class, 'special_offer_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function link(): string
    {
        return route('offer.show', $this->token);
    }
}
