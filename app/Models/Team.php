<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'position',
        'social',
        'bio',
        'year',
        'edition_id',
    ];

    protected $casts = [
        'social' => 'array',
    ];

    public function edition()
    {
        return $this->belongsTo(Edition::class);
    }
}
