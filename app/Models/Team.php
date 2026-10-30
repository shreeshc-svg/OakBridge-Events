<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        'sort_order',
    ];

    protected $casts = [
        'social' => 'array',
    ];

    public function edition()
    {
        return $this->belongsTo(Edition::class);
    }

    protected static function booted(): void
    {
        // new people, and people moved to another year or type, go to the end of that list
        static::creating(function (Team $team) {
            if (! $team->sort_order) {
                $team->sort_order = self::nextSortOrder();
            }
        });
        static::updating(function (Team $team) {
            if ($team->isDirty('edition_id') || $team->isDirty('year')) {
                $team->sort_order = self::nextSortOrder();
            }
        });
    }

    /** The order set in Admin (drag and drop), oldest first for ties. */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public static function nextSortOrder(): int
    {
        return (int) self::max('sort_order') + 1;
    }

    /**
     * Put these people in this order. They take over the places they already hold,
     * so anyone not in the list (another year, a filtered-out row) keeps their place.
     */
    public static function reorder(array $ids): void
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));

        DB::transaction(function () use ($ids) {
            $known = self::whereIn('id', $ids)->pluck('id')->all();
            $ids = array_values(array_filter($ids, fn ($id) => in_array($id, $known, true)));
            $slots = self::whereIn('id', $ids)->lockForUpdate()->pluck('sort_order')->sort()->values();

            // two people sharing a place: number everyone 1, 2, 3… first (same order as now)
            if ($slots->unique()->count() !== $slots->count()) {
                foreach (self::ordered()->pluck('id') as $i => $id) {
                    DB::table('teams')->where('id', $id)->update(['sort_order' => $i + 1]);
                }
                $slots = self::whereIn('id', $ids)->pluck('sort_order')->sort()->values();
            }

            foreach ($ids as $i => $id) {
                DB::table('teams')->where('id', $id)->update(['sort_order' => $slots[$i]]);
            }
        });
    }
}
