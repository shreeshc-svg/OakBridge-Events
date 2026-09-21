<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PassBundle extends Model
{
    protected $guarded = [];

    protected $casts = [
        'quantity' => 'integer',
        'total' => 'decimal:2',
        'early_total' => 'decimal:2',
    ];

    public function passType()
    {
        return $this->belongsTo(PassType::class);
    }

    /** Bundle total for the price mode in force, or null when this row has no price. */
    public function totalFor(bool $early): ?float
    {
        $value = $early ? ($this->early_total ?? $this->total) : $this->total;

        return $value === null ? null : (float) $value;
    }
}
