<?php

namespace App\Support;

use App\Models\Edition;
use App\Models\Gallery;
use App\Models\Sponsor;
use App\Models\Team;
use App\Models\Video;
use Illuminate\Support\Collection;

/**
 * Which years have something to show, per section of the site. A year only
 * appears under a header tab (and as a tab on its page) when it has content
 * for that section and is not hidden in Admin > Years.
 */
class Editions
{
    /** Sections a header tab can turn into a year flyout. */
    public const SECTIONS = [
        'speakers' => 'Speakers',
        'schedule' => 'Schedule',
        'sponsors' => 'Sponsors',
        'gallery' => 'Gallery (Images / Videos)',
    ];

    /**
     * Visible years, newest first, each with what it holds.
     *
     * @return Collection<int, array{edition: Edition, speakers: int, sponsors: int, images: int, videos: int, schedule: ?\App\Models\Service}>
     */
    public static function index(): Collection
    {
        // worked out once per request
        $memo = request()->attributes;
        if ($memo->has('editions.index')) {
            return $memo->get('editions.index');
        }

        try {
            $editions = Edition::where('is_visible', true)->orderByDesc('year')->with('scheduleService')->get();

            $count = fn ($query) => $query->whereNotNull('edition_id')
                ->selectRaw('edition_id, count(*) as aggregate')->groupBy('edition_id')->pluck('aggregate', 'edition_id');

            $speakers = $count(Team::where('year', 'Speaker'));
            $sponsors = $count(Sponsor::where('is_active', true)
                ->whereIn('sponsor_group_id', \App\Models\SponsorGroup::where('is_active', true)->select('id')));
            $images = $count(Gallery::query());
            $videos = $count(Video::query());

            $index = $editions->map(fn (Edition $e) => [
                'edition' => $e,
                'speakers' => (int) ($speakers[$e->id] ?? 0),
                'sponsors' => (int) ($sponsors[$e->id] ?? 0),
                'images' => (int) ($images[$e->id] ?? 0),
                'videos' => (int) ($videos[$e->id] ?? 0),
                'schedule' => $e->scheduleService && $e->scheduleService->published === '1' ? $e->scheduleService : null,
            ])->values();
        } catch (\Throwable $e) {
            // tables not migrated yet
            $index = collect();
        }

        $memo->set('editions.index', $index);

        return $index;
    }

    /** Years that have content for a section ('speakers', 'sponsors', 'schedule', 'gallery', 'images', 'videos'). */
    public static function years(string $section): Collection
    {
        return self::index()->filter(fn ($row) => match ($section) {
            'gallery' => $row['images'] > 0 || $row['videos'] > 0,
            'schedule' => $row['schedule'] !== null,
            default => ($row[$section] ?? 0) > 0,
        })->values();
    }

    public static function latest(string $section): ?Edition
    {
        return self::years($section)->first()['edition'] ?? null;
    }

    /** The year asked for, if it has content for the section; the newest one when no year is given. */
    public static function pick(string $section, $year = null): ?Edition
    {
        if ($year === null || $year === '') {
            return self::latest($section);
        }

        return self::years($section)->first(fn ($row) => $row['edition']->year === (int) $year)['edition'] ?? null;
    }

    /** Forget the cached index (after admin changes within the same request, and between tests). */
    public static function forget(): void
    {
        request()->attributes->remove('editions.index');
    }

    /** Year options for admin forms. */
    public static function options(): Collection
    {
        try {
            return Edition::orderByDesc('year')->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    /** Header children for a tab whose links are built from the years. */
    public static function menuChildren(string $section): array
    {
        $link = fn ($label, $url, $children = []) => ['label' => $label, 'url' => $url, 'new_tab' => false, 'children' => $children];

        return self::years($section)->map(function ($row) use ($section, $link) {
            $year = $row['edition']->year;

            return match ($section) {
                'speakers' => $link((string) $year, '/speakers/' . $year),
                'sponsors' => $link((string) $year, '/sponsors/' . $year),
                'schedule' => $link((string) $year, '/event/' . $row['schedule']->slug),
                'gallery' => $link(
                    (string) $year,
                    $row['images'] ? '/gallery/' . $year : '/videos/' . $year,
                    array_values(array_filter([
                        $row['images'] ? $link('Images', '/gallery/' . $year) : null,
                        $row['videos'] ? $link('Videos', '/videos/' . $year) : null,
                    ]))
                ),
                default => null,
            };
        })->filter()->values()->all();
    }
}
