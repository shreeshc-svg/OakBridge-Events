<?php

namespace App\Support;

use App\Models\PageSeo;
use Illuminate\Support\Facades\Route;

/**
 * Per-page title / description / keywords, managed from Admin > SEO.
 * Anything left empty keeps the page's built-in value.
 */
class Seo
{
    /** route name => [label, view that holds the built-in values] */
    public const PAGES = [
        'home'           => ['Homepage', 'frontend/index'],
        'about'          => ['About', 'frontend/about'],
        'speakers'       => ['Speakers', 'frontend/speakers'],
        'advisors'       => ['Advisors', 'frontend/advisors'],
        'events'         => ['Events list', 'frontend/events'],
        'gallery'        => ['Gallery (images)', 'frontend/gallery'],
        'videos'         => ['Videos', 'frontend/videos'],
        'testimonial'    => ['Testimonials', 'frontend/testimonial'],
        'contact'        => ['Contact', 'frontend/contact'],
        'privacy.policy' => ['Privacy Policy', 'frontend/privacy'],
        'legathan'       => ['Legathon', 'frontend/legathan'],
        'faq'            => ['FAQ', 'frontend/faq'],
        'blog'           => ['Blog', 'frontend/blog'],
    ];

    /** other route names that show the same page */
    private const ALIASES = ['image.gallery' => 'gallery'];

    public static function pageKey(?string $routeName = null): ?string
    {
        $name = $routeName ?? optional(Route::current())->getName();
        $name = self::ALIASES[$name] ?? $name;

        return isset(self::PAGES[$name]) ? $name : null;
    }

    private static function row(): ?PageSeo
    {
        // cached for the current request only
        if (! app()->bound('site.seo.row')) {
            $row = null;
            $key = self::pageKey();
            if ($key) {
                try {
                    $row = PageSeo::where('page', $key)->first();
                } catch (\Throwable $e) {
                    $row = null;
                }
            }
            app()->instance('site.seo.row', ['row' => $row]);
        }

        return app('site.seo.row')['row'];
    }

    public static function title(?string $fallback): string
    {
        return trim((string) (self::row()?->title ?: $fallback));
    }

    public static function description(?string $fallback): string
    {
        return trim((string) (self::row()?->description ?: $fallback));
    }

    public static function keywords(?string $fallback): string
    {
        return trim((string) (self::row()?->keywords ?: $fallback));
    }

    /** Built-in value written in the page template, for showing in the admin. */
    public static function builtIn(string $page, string $field): ?string
    {
        $view = self::PAGES[$page][1] ?? null;
        $file = $view ? resource_path('views/' . $view . '.blade.php') : null;
        if (! $file || ! is_file($file)) {
            return null;
        }
        $source = file_get_contents($file);
        if (preg_match("/@section\\('" . preg_quote($field, '/') . "',\\s*'((?:[^'\\\\]|\\\\.)*)'\\s*\\)/", $source, $m)) {
            return stripslashes($m[1]);
        }
        if (preg_match("/@section\\('" . preg_quote($field, '/') . "',/", $source)) {
            return '(built from Settings)';
        }

        return null;
    }
}
