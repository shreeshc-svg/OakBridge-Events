<?php

namespace App\Support;

use App\Models\MenuItem;

/**
 * Header and footer links, managed from Admin > Menus.
 * Falls back to the original links if the menu tables are not there yet.
 */
class SiteMenu
{
    public const LOCATIONS = ['header' => 'Header menu', 'footer' => 'Footer "Useful Links"'];

    /** @return array<int, array{label:string,url:?string,new_tab:bool,children:array}> */
    public static function items(string $location): array
    {
        $cacheKey = 'site.menu.' . $location;
        if (app()->bound($cacheKey)) {
            return app($cacheKey);
        }

        try {
            $rows = MenuItem::where('location', $location)
                ->whereNull('parent_id')
                ->where('is_active', true)
                ->orderBy('sort_order')->orderBy('id')
                ->with(['children' => fn ($q) => $q->where('is_active', true)])
                ->get();
            $items = $rows->map(fn ($item) => [
                'label' => $item->label,
                'url' => $item->url,
                'new_tab' => (bool) $item->new_tab,
                'children' => $item->children->map(fn ($child) => [
                    'label' => $child->label,
                    'url' => $child->url,
                    'new_tab' => (bool) $child->new_tab,
                    'children' => [],
                ])->all(),
            ])->all();
        } catch (\Throwable $e) {
            $items = self::defaults($location);
        }

        app()->instance($cacheKey, $items);

        return $items;
    }

    public static function href(?string $url): string
    {
        if ($url === null || $url === '' || $url === '#') {
            return '#';
        }
        if (preg_match('#^([a-z][a-z0-9+.-]*:|//)#i', $url)) {
            return $url;
        }

        return url($url);
    }

    /** True when the link points at the page being viewed (anchors on other pages don't count). */
    public static function isCurrent(array $item): bool
    {
        foreach ($item['children'] ?? [] as $child) {
            if (self::isCurrent($child)) {
                return true;
            }
        }
        $url = $item['url'] ?? '';
        if ($url === '' || $url === '#' || str_contains($url, '#')) {
            return false;
        }
        $path = parse_url(self::href($url), PHP_URL_PATH) ?: '/';
        $host = parse_url(self::href($url), PHP_URL_HOST);
        if ($host && $host !== request()->getHost()) {
            return false;
        }

        return '/' . trim($path, '/') === '/' . trim(request()->getPathInfo(), '/');
    }

    private static function defaults(string $location): array
    {
        $link = fn ($label, $url, $children = []) => ['label' => $label, 'url' => $url, 'new_tab' => false, 'children' => $children];
        $schedule = '/event/india-law-ai-tech-summit-2025';

        if ($location === 'footer') {
            return [
                $link('Speakers', '/speakers'),
                $link('Schedule', $schedule),
                $link('Sponsors', '/#sponsors'),
                $link('Contact Us', '/contact'),
                $link('Privacy Policy', '/privacy-policy'),
            ];
        }

        return [
            $link('Home', '/'),
            $link('Speakers', '/speakers'),
            $link('Schedule', $schedule),
            $link('Sponsors', '/#sponsors'),
            $link('Exhibitors', '/#exhibitors'),
            $link('Gallery', '#', [$link('Images', '/gallery'), $link('Videos', '/videos')]),
            $link('About', '/about'),
        ];
    }
}
