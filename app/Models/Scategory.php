<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Service;
use App\Models\Scategory;

class Scategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'featured',
        'parent_id'
    ];

    public function subcategory()
    {
        return $this->hasMany(Scategory::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Scategory::class, 'parent_id');
    }


    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }
}
