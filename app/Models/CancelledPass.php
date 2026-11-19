<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** A pass that was cancelled or deleted – kept so the numbers can always be accounted for. */
class CancelledPass extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'issued_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'amount' => 'decimal:2',
    ];
}
