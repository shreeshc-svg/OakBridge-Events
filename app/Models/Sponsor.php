<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    protected $guarded = [];

    protected $casts = ['is_active' => 'boolean'];

    public function group()
    {
        return $this->belongsTo(SponsorGroup::class, 'sponsor_group_id');
    }
}
