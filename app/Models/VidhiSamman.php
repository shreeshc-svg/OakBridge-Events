<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VidhiSamman extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Explicitly specify the table name
    protected $table = 'vidhi_sammen';

}
