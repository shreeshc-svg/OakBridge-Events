<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** A GST tax invoice. Everything printed on it lives in `snapshot` and never changes. */
class Invoice extends Model
{
    protected $guarded = [];

    protected $casts = [
        'snapshot' => 'array',
        'issued_at' => 'datetime',
        'last_sent_at' => 'datetime',
        'seq' => 'integer',
        'sent_count' => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /** OBE/2026-27/0001 -> OBE-2026-27-0001.pdf */
    public function filename(): string
    {
        return 'Invoice-' . str_replace('/', '-', $this->number) . '.pdf';
    }
}
