<?php

namespace App\Support;

/** Helpers for the homepage hero banner, which can be an image or a video. */
class HeroMedia
{
    public const DIR = 'public/uploads/images/hero/';
    public const DEFAULT_IMAGE = 'public/assets/images/website_banner.webp';
    public const VIDEO_EXTENSIONS = ['mp4', 'webm'];

    public static function isVideo(?string $file): bool
    {
        return $file !== null && in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), self::VIDEO_EXTENSIONS, true);
    }

    public static function url(?string $file): ?string
    {
        return $file ? asset(self::DIR . $file) : null;
    }

    public static function mimeType(string $file): string
    {
        return strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'webm' ? 'video/webm' : 'video/mp4';
    }
}
