<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Carbon;

/**
 * The marketing popup on the home page: an image, GIF or short video that the
 * admin uploads, optionally linked, optionally scheduled to a date range.
 */
class PromoBanner
{
    /** Folder (inside public/) holding the uploaded files. */
    public const DIR = 'public/uploads/images/promo/';

    public const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    public const VIDEO_EXTENSIONS = ['mp4', 'webm'];

    /** Upload ceilings, in kilobytes. GIFs get more room than a flat image. */
    public const MAX_IMAGE_KB = 5120;    // 5 MB
    public const MAX_GIF_KB = 15360;     // 15 MB
    public const MAX_VIDEO_KB = 51200;   // 50 MB

    public const MIN_DELAY = 0;
    public const MAX_DELAY = 120;

    public static function extension(?string $file): string
    {
        return strtolower(pathinfo((string) $file, PATHINFO_EXTENSION));
    }

    public static function isVideo(?string $file): bool
    {
        return $file !== null && in_array(self::extension($file), self::VIDEO_EXTENSIONS, true);
    }

    public static function isGif(?string $file): bool
    {
        return $file !== null && self::extension($file) === 'gif';
    }

    public static function url(?string $file): ?string
    {
        return $file ? asset(self::DIR . $file) : null;
    }

    public static function mimeType(?string $file): string
    {
        return self::extension($file) === 'webm' ? 'video/webm' : 'video/mp4';
    }

    /** Kilobyte ceiling that applies to one uploaded file. */
    public static function maxKbFor(?string $extension): int
    {
        return match (strtolower((string) $extension)) {
            'mp4', 'webm' => self::MAX_VIDEO_KB,
            'gif' => self::MAX_GIF_KB,
            default => self::MAX_IMAGE_KB,
        };
    }

    /**
     * Should visitors see the popup right now?
     *
     * Switched on, a file uploaded, and today inside the scheduled window.
     */
    public static function isLive(?Setting $setting, ?Carbon $now = null): bool
    {
        if (! $setting || ! $setting->promo_enabled || ! $setting->promo_media) {
            return false;
        }

        $today = ($now ?: Carbon::now())->startOfDay();

        if ($setting->promo_starts_at && $today->lt(Carbon::parse($setting->promo_starts_at)->startOfDay())) {
            return false;
        }

        if ($setting->promo_ends_at && $today->gt(Carbon::parse($setting->promo_ends_at)->startOfDay())) {
            return false;
        }

        return true;
    }

    /** Why the popup is not showing, in words an admin can act on. */
    public static function statusNote(?Setting $setting, ?Carbon $now = null): string
    {
        if (! $setting || ! $setting->promo_media) {
            return 'No file uploaded yet, so nothing is showing.';
        }

        if (! $setting->promo_enabled) {
            return 'Switched off. Visitors are not seeing it.';
        }

        $today = ($now ?: Carbon::now())->startOfDay();

        if ($setting->promo_starts_at && $today->lt(Carbon::parse($setting->promo_starts_at)->startOfDay())) {
            return 'Scheduled – it starts showing on ' . Carbon::parse($setting->promo_starts_at)->format('j M Y') . '.';
        }

        if ($setting->promo_ends_at && $today->gt(Carbon::parse($setting->promo_ends_at)->startOfDay())) {
            return 'Finished – it stopped showing after ' . Carbon::parse($setting->promo_ends_at)->format('j M Y') . '.';
        }

        return $setting->promo_ends_at
            ? 'Live on the home page until ' . Carbon::parse($setting->promo_ends_at)->format('j M Y') . '.'
            : 'Live on the home page.';
    }

    /**
     * How wide the popup may render, in CSS.
     *
     * Never wider than the file's own pixel width, so a small upload is shown
     * at its true size instead of being stretched and going soft.
     */
    public static function maxWidthCss(?Setting $setting): string
    {
        $natural = (int) ($setting->promo_width ?? 0);
        $cap = $natural > 0 ? min($natural, 760) : 760;

        return 'min(92vw, ' . $cap . 'px)';
    }
}
