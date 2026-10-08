<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** One year of the event: its speakers, sponsors, gallery and schedule. */
class Edition extends Model
{
    protected $guarded = [];

    protected $casts = [
        'year' => 'integer',
        'is_visible' => 'boolean',
    ];

    public function scheduleService()
    {
        return $this->belongsTo(Service::class, 'schedule_service_id');
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function sponsors()
    {
        return $this->hasMany(Sponsor::class);
    }

    public function galleries()
    {
        return $this->hasMany(Gallery::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    /** How many items are tagged with this year, by kind. */
    public function contentCounts(): array
    {
        return [
            'speakers' => $this->teams()->count(),
            'sponsors' => $this->sponsors()->count(),
            'images' => $this->galleries()->count(),
            'videos' => $this->videos()->count(),
        ];
    }

    public function label(): string
    {
        return (string) $this->year;
    }
}
