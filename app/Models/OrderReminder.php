<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** One payment reminder that went (or failed to go) to a buyer. */
class OrderReminder extends Model
{
    protected $guarded = [];

    protected $casts = [
        'stage' => 'integer',
        'failed' => 'boolean',
        'sent_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /** "3-day reminder", "Sent by hand", and so on. */
    public function label(): string
    {
        if ($this->kind === 'manual') {
            return 'Sent by hand' . ($this->sent_by ? ' by ' . $this->sent_by : '');
        }

        return match ((int) $this->stage) {
            1 => 'Automatic – 1 day after the order',
            2 => 'Automatic – 3 days after the order',
            3 => 'Automatic – 7 days after the order (last one)',
            default => 'Automatic',
        };
    }
}
