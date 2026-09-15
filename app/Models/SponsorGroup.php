<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SponsorGroup extends Model
{
    protected $guarded = [];

    protected $casts = ['is_active' => 'boolean'];

    /** Bootstrap column classes for each logo size. */
    public const SIZES = [
        'large'  => ['label' => 'Large – own row', 'class' => 'col-lg-7 col-md-6 col-sm-12'],
        'medium' => ['label' => 'Medium', 'class' => 'col-lg-5 col-md-6 col-sm-12'],
        'small'  => ['label' => 'Small', 'class' => 'col-lg-4 col-md-6 col-sm-12'],
    ];

    public function sponsors()
    {
        return $this->hasMany(Sponsor::class)->orderBy('sort_order')->orderBy('id');
    }

    public function activeSponsors()
    {
        return $this->sponsors()->where('is_active', true);
    }

    public function columnClass(): string
    {
        return self::SIZES[$this->logo_size]['class'] ?? self::SIZES['small']['class'];
    }
}
