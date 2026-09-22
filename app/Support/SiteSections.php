<?php

namespace App\Support;

use App\Models\Setting;

/**
 * Sections an admin can switch off without deleting their content (Admin > Page Content).
 *
 * Most sections are stored as a list of hidden keys in settings.hidden_sections.
 * The hero banner and marketing strip keep using their own on/off settings,
 * so the switch here and the one on their own admin pages always agree.
 */
class SiteSections
{
    /** Keys in the order they appear on each page. */
    public static function all(): array
    {
        return [
            'Homepage' => [
                'home.hero' => ['label' => 'Hero banner', 'setting' => 'hero_enabled', 'route' => 'hero.edit'],
                'home.strip' => ['label' => 'Marketing strip', 'setting' => 'strip_enabled', 'route' => 'strip.edit',
                    'hint' => 'Shows only when it has at least one phrase.'],
                'home.intro' => ['label' => 'Intro and highlight cards'],
                'home.overview' => ['label' => 'Event overview (6 features)'],
                'home.audience' => ['label' => 'Who should attend'],
                'home.speakers' => ['label' => 'Speakers section'],
                'home.programme' => ['label' => 'Programme section', 'hint' => 'Homepage only – the /events page keeps it.'],
                'home.sponsors' => ['label' => 'Partners & sponsors', 'route' => 'sponsors.index',
                    'hint' => 'If you hide it, also hide the Sponsors / Exhibitors links in Menus.'],
                'home.register' => ['label' => 'Register section', 'hint' => 'Also hidden automatically while registration is closed.'],
            ],
            'About' => [
                'about.organizer' => ['label' => 'About the Organizer'],
                'about.team' => ['label' => 'Our Team section'],
            ],
        ];
    }

    /** Homepage sections, top to bottom, as the site shipped. */
    public static function homeDefaultOrder(): array
    {
        return array_keys(self::all()['Homepage']);
    }

    /**
     * Homepage sections in the order the admin arranged them.
     *
     * Anything stored but no longer a real section is dropped, and any section
     * added to the code later is slotted in at its default position, so a saved
     * order never hides a new section or breaks on an old one.
     */
    public static function homeOrder(): array
    {
        $default = self::homeDefaultOrder();
        $saved = json_decode((string) (self::setting()?->home_section_order ?? ''), true);

        if (! is_array($saved)) {
            return $default;
        }

        $order = [];
        foreach ($saved as $key) {
            if (is_string($key) && in_array($key, $default, true) && ! in_array($key, $order, true)) {
                $order[] = $key;
            }
        }

        // sections the saved order never knew about go back where they belong
        foreach ($default as $position => $key) {
            if (! in_array($key, $order, true)) {
                array_splice($order, min($position, count($order)), 0, [$key]);
            }
        }

        return $order;
    }

    /** Save a new order. Passing an empty list restores the original one. */
    public static function setHomeOrder(array $keys): array
    {
        $default = self::homeDefaultOrder();
        $clean = [];

        foreach ($keys as $key) {
            if (is_string($key) && in_array($key, $default, true) && ! in_array($key, $clean, true)) {
                $clean[] = $key;
            }
        }

        $setting = Setting::findOrFail(1);
        $setting->home_section_order = ($clean && $clean !== $default) ? json_encode($clean) : null;
        $setting->save();

        app()->forgetInstance('site.sections.setting');

        return self::homeOrder();
    }

    public static function homeOrderIsDefault(): bool
    {
        return self::homeOrder() === self::homeDefaultOrder();
    }

    /** 'home.intro' -> 'intro', the partial under frontend/sections/home/. */
    public static function homeView(string $key): string
    {
        return str_replace('home.', '', $key);
    }

    public static function find(string $key): ?array
    {
        foreach (self::all() as $sections) {
            if (isset($sections[$key])) {
                return $sections[$key];
            }
        }

        return null;
    }

    public static function isVisible(string $key): bool
    {
        $section = self::find($key);
        if (! $section) {
            return true;
        }
        $setting = self::setting();
        if (! $setting) {
            return true;
        }
        if (isset($section['setting'])) {
            // hero is on unless switched off; the strip is off unless switched on
            $value = $setting->{$section['setting']};

            return $section['setting'] === 'hero_enabled' ? $value === null || (bool) $value : (bool) $value;
        }

        return ! in_array($key, self::hiddenKeys($setting), true);
    }

    public static function setVisible(string $key, bool $visible): void
    {
        $section = self::find($key) ?? throw new \InvalidArgumentException("Unknown section {$key}");
        $setting = Setting::findOrFail(1);

        if (isset($section['setting'])) {
            $setting->{$section['setting']} = $visible;
        } else {
            $hidden = array_values(array_diff(self::hiddenKeys($setting), [$key]));
            if (! $visible) {
                $hidden[] = $key;
            }
            $setting->hidden_sections = $hidden ? json_encode($hidden) : null;
        }
        $setting->save();

        app()->forgetInstance('site.sections.setting');
    }

    private static function hiddenKeys(Setting $setting): array
    {
        $keys = json_decode((string) ($setting->hidden_sections ?? ''), true);

        return is_array($keys) ? $keys : [];
    }

    private static function setting(): ?Setting
    {
        if (! app()->bound('site.sections.setting')) {
            try {
                $setting = Setting::find(1);
            } catch (\Throwable $e) {
                $setting = null;
            }
            app()->instance('site.sections.setting', ['row' => $setting]);
        }

        return app('site.sections.setting')['row'];
    }
}
