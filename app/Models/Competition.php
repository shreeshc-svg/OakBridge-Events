<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Competition extends Model
{
    protected $guarded = [];

    protected $casts = [
        'buttons' => 'array',
        'is_active' => 'boolean',
    ];
}
