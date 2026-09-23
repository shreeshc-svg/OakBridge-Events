<?php

namespace App\Support;

use App\Models\PageSection;

/**
 * Editable text blocks for the public pages (Admin > Page Content).
 *
 * Each section lists its fields and the text the site showed before the
 * editor existed. Saved values override those defaults field by field.
 * A default of "view:some.view" is rendered from that Blade view.
 */
class PageContent
{
    /** field types: text, textarea, richtext, image, file, toggle */
    public static function definitions(): array
    {
        $icon = ['type' => 'text', 'label' => 'Icon', 'help' => 'Icon class, e.g. fa fa-medal'];

        return [
            // ------------------------------------------------------------ Homepage
            'home.intro' => [
                'page' => 'Homepage',
                'label' => 'Intro and highlight cards',
                'hint' => 'The opening block of copy and the cards under it.',
                'fields' => [
                    'eyebrow' => ['type' => 'text', 'label' => 'Small title', 'default' => 'Forging the Future of Law, AI & Tech'],
                    'body' => ['type' => 'richtext', 'label' => 'Intro text', 'default' => '<p>The India Law, AI &amp; Tech Summit envisions India’s premier annual forum for legal innovation. A dynamic experience designed for maximum engagement and unparalled access, celebrating Law, AI &amp; Tech pioneers, leaders, and innovators driving Law, AI &amp; Tech revolution.</p>'],
                ],
                'items' => [
                    'label' => 'Highlight cards',
                    'max' => 8,
                    'fields' => [
                        'icon' => array_merge($icon, ['help' => 'Font Awesome class, e.g. fa fa-medal, fa fa-handshake, fa fa-globe, fa fa-book']),
                        'title' => ['type' => 'text', 'label' => 'Title'],
                        'text' => ['type' => 'textarea', 'label' => 'Text'],
                    ],
                    'default' => [
                        ['icon' => 'fa fa-medal', 'title' => "Establishing India's Premier Law, AI & Tech Event", 'text' => 'Creating the definitive annual event for Law, Technology and Innovation in the region.'],
                        ['icon' => 'fa fa-handshake', 'title' => 'Uniting Key Stakeholders', 'text' => 'Bringing together Top Law Firms, General Counsels, Policymakers, Technologists, and Innovators to foster collaboration.'],
                        ['icon' => 'fa fa-globe', 'title' => 'Shaping the Future of Law Roadmap', 'text' => "Influencing the adoption and evolution of AI & Tech solutions across India's legal landscape."],
                        ['icon' => 'fa fa-book', 'title' => "Envisioning the 'Davos of Law, AI & Tech'", 'text' => 'Becoming the essential annual gathering for Thought Leadership and Networking in Law, AI & Tech.'],
                    ],
                ],
            ],
            'home.overview' => [
                'page' => 'Homepage',
                'label' => 'Event overview (6 features)',
                'fields' => [
                    'eyebrow' => ['type' => 'text', 'label' => 'Small title', 'default' => 'A Premium Summit Experience'],
                    'heading' => ['type' => 'text', 'label' => 'Heading', 'default' => 'Event Overview'],
                ],
                'items' => [
                    'label' => 'Features',
                    'max' => 12,
                    'fields' => [
                        'icon' => array_merge($icon, ['help' => 'Theme icon class, e.g. flaticon-lecture, flaticon-employee, flaticon-user, flaticon-tv, flaticon-diamond']),
                        'title' => ['type' => 'text', 'label' => 'Title'],
                        'text' => ['type' => 'textarea', 'label' => 'Text'],
                    ],
                    'default' => [
                        ['icon' => 'flaticon-lecture', 'title' => 'Full Day Premium Summit', 'text' => 'An immersive experience designed for deep dives and high-level discourse.'],
                        ['icon' => 'flaticon-employee-1', 'title' => 'Powerful Keynote Sessions', 'text' => 'Sharp insights and big ideas shaping the future of Law, AI & Tech.'],
                        ['icon' => 'flaticon-employee', 'title' => 'Engaging Fireside Chats', 'text' => 'Candid perspectives and experience from the frontlines of legal tech.'],
                        ['icon' => 'flaticon-user', 'title' => 'Interactive Panel Discussions', 'text' => 'Engaging debates and diverse perspectives on critical topics.'],
                        ['icon' => 'flaticon-tv', 'title' => 'Law, AI & Tech Solution Providers', 'text' => 'Discover cutting-edge solutions and explore new technologies.'],
                        ['icon' => 'flaticon-diamond', 'title' => 'Elite Networking Opportunities', 'text' => 'Exclusive Knowledge Summit with dedicated networking lounge for unparalleled connections.'],
                    ],
                ],
            ],
            'home.audience' => [
                'page' => 'Homepage',
                'label' => 'Who should attend',
                'fields' => [
                    'eyebrow' => ['type' => 'text', 'label' => 'Small title', 'default' => 'A Curated Gathering of Legal Luminaries & Innovators'],
                    'heading' => ['type' => 'text', 'label' => 'Heading', 'default' => 'Who Should Attend?'],
                    'image' => ['type' => 'image', 'label' => 'Image', 'default' => 'public/assets/images/why.webp'],
                    'button_label' => ['type' => 'text', 'label' => 'Register button text', 'help' => 'Shown only while registration is open.', 'default' => 'Register Now'],
                ],
                'items' => [
                    'label' => 'List',
                    'max' => 12,
                    'fields' => ['text' => ['type' => 'text', 'label' => 'Line']],
                    'default' => [
                        ['text' => 'Managing Partners/Sr Partners of Top Law Firms'],
                        ['text' => "General Counsels of India's Top Corporates"],
                        ['text' => 'CTOs/CIOs of Top Law Firms & Corporates'],
                        ['text' => 'AI & Tech Solutions Providers'],
                        ['text' => 'Regulators, Law and Policymakers, Judiciary'],
                    ],
                ],
            ],
            'home.speakers' => [
                'page' => 'Homepage',
                'label' => 'Speakers section',
                'hint' => 'The speakers themselves are managed in Speakers / Advisors / Team.',
                'fields' => [
                    'eyebrow' => ['type' => 'text', 'label' => 'Small title', 'help' => 'Leave empty to use the business name from Settings.', 'default' => ''],
                    'heading' => ['type' => 'text', 'label' => 'Heading', 'default' => 'Distinguished Speakers'],
                    'button_label' => ['type' => 'text', 'label' => 'Button text', 'default' => 'View All'],
                ],
            ],
            'home.programme' => [
                'page' => 'Homepage',
                'label' => 'Programme section (also on /events)',
                'hint' => 'The events themselves are managed in Events.',
                'fields' => [
                    'eyebrow' => ['type' => 'text', 'label' => 'Small title', 'help' => 'Leave empty to use the business name from Settings.', 'default' => ''],
                    'heading' => ['type' => 'text', 'label' => 'Heading', 'default' => 'Programme'],
                ],
            ],
            'home.sponsors' => [
                'page' => 'Homepage',
                'label' => 'Sponsors section heading',
                'hint' => 'Logos are managed in Sponsors & Exhibitors.',
                'fields' => [
                    'heading' => ['type' => 'text', 'label' => 'Heading', 'default' => 'Partners & Sponsors'],
                ],
            ],
            'home.register' => [
                'page' => 'Homepage',
                'label' => 'Register section',
                'hint' => 'Shown only while registration is open.',
                'fields' => [
                    'heading' => ['type' => 'text', 'label' => 'Heading', 'default' => 'Register Now'],
                    'body' => ['type' => 'textarea', 'label' => 'Text', 'default' => 'Be part of this vibrant summit that brings together experts and enthusiasts on Legal Tech and AI to discuss and ideate on the profound connections between law and tech.'],
                ],
            ],

            // ------------------------------------------------------------ About
            'about.organizer' => [
                'page' => 'About',
                'label' => 'About the Organizer',
                'fields' => [
                    'page_title' => ['type' => 'text', 'label' => 'Page banner title', 'default' => 'About Us'],
                    'heading' => ['type' => 'text', 'label' => 'Heading', 'default' => 'About the Organizer'],
                    'body' => ['type' => 'richtext', 'label' => 'Text', 'default' => 'view:frontend.content.about-organizer'],
                ],
            ],
            'about.team' => [
                'page' => 'About',
                'label' => 'Our Team section',
                'hint' => 'Team members are managed in Speakers / Advisors / Team (type "Organizer team").',
                'fields' => [
                    'eyebrow' => ['type' => 'text', 'label' => 'Small title', 'help' => 'Leave empty to use the business name from Settings.', 'default' => ''],
                    'heading' => ['type' => 'text', 'label' => 'Heading', 'default' => 'Our Team'],
                ],
            ],

            // ------------------------------------------------------------ Schedule
            'schedule' => [
                'page' => 'Schedule',
                'label' => 'Schedule page',
                'hint' => 'Applies to every event page. Each event\'s date, timings, venue, sessions and agenda are edited in Events › Schedules.',
                'fields' => [
                    'eyebrow' => ['type' => 'text', 'label' => 'Small title', 'default' => 'Event Agenda'],
                    'heading' => ['type' => 'text', 'label' => 'Heading', 'default' => 'Schedule'],
                    'empty_message' => ['type' => 'text', 'label' => 'Message when an event has no sessions yet', 'default' => 'The full schedule will be announced soon.'],

                    'register_show' => ['type' => 'toggle', 'label' => 'Show the Register button', 'help' => 'It is hidden anyway while registration is closed.', 'default' => '1'],
                    'register_label' => ['type' => 'text', 'label' => 'Register button text', 'default' => 'Register Now'],

                    'agenda_show' => ['type' => 'toggle', 'label' => 'Show the Agenda button', 'default' => '1'],
                    'agenda_label' => ['type' => 'text', 'label' => 'Agenda button text', 'default' => 'Download Agenda'],
                    'agenda_pending_message' => ['type' => 'text', 'label' => 'Message while no agenda PDF is uploaded', 'help' => 'Shown in place of the button until an agenda is ready. Leave empty to show nothing at all.', 'default' => 'The agenda will be uploaded soon.'],
                    'agenda_file' => ['type' => 'file', 'label' => 'Default agenda PDF', 'help' => 'Used when an event has no agenda PDF of its own.', 'default' => 'public/uploads/Agenda_29_Nov.pdf'],
                ],
            ],

            // ------------------------------------------------------------ Legathon
            'legathon' => [
                'page' => 'Legathon',
                'label' => 'Legathon page',
                'hint' => 'The competitions are managed in Legathon.',
                'fields' => [
                    'page_title' => ['type' => 'text', 'label' => 'Page banner title', 'default' => 'Legathon'],
                ],
            ],

            // ------------------------------------------------------------ Privacy
            'privacy' => [
                'page' => 'Privacy Policy',
                'label' => 'Privacy Policy',
                'fields' => [
                    'page_title' => ['type' => 'text', 'label' => 'Page banner title', 'default' => 'Privacy Policy'],
                    'heading' => ['type' => 'text', 'label' => 'Heading', 'default' => 'PRIVACY POLICY – ILATS Website'],
                    'body' => ['type' => 'richtext', 'label' => 'Policy text', 'default' => 'view:frontend.content.privacy'],
                ],
            ],

            // ------------------------------------------------------------ Footer
            'footer' => [
                'page' => 'Footer',
                'label' => 'Footer text',
                'hint' => 'Footer links are managed in Menus. Phone, email, address and social links come from Settings.',
                'fields' => [
                    'body' => ['type' => 'textarea', 'label' => 'About text', 'default' => 'OakBridge Publishing is a new-age publishing and knowledge services organization, established with the objective of redeﬁning the craft of publishing to address the evolving requirements of today’s professionals and academia.'],
                    'links_title' => ['type' => 'text', 'label' => 'Links column title', 'default' => 'Useful Links'],
                    'contact_title' => ['type' => 'text', 'label' => 'Contact column title', 'default' => 'Contact Us'],
                    'copyright' => ['type' => 'text', 'label' => 'Copyright line', 'default' => '©2025 OakBridge Publishing'],
                ],
            ],
        ];
    }

    public static function definition(string $key): ?array
    {
        return self::definitions()[$key] ?? null;
    }

    /** Sections grouped by page, for the admin list. */
    public static function pages(): array
    {
        $pages = [];
        foreach (self::definitions() as $key => $def) {
            $pages[$def['page']][$key] = $def;
        }

        return $pages;
    }

    /** Saved rows for this request, keyed by section key. */
    private static function saved(): array
    {
        if (! app()->bound('site.page_sections')) {
            try {
                $rows = PageSection::all()->pluck('data', 'key')->all();
            } catch (\Throwable $e) {
                $rows = [];
            }
            app()->instance('site.page_sections', $rows);
        }

        return app('site.page_sections');
    }

    public static function isCustomised(string $key): bool
    {
        return array_key_exists($key, self::saved());
    }

    /**
     * Values for a section: saved values where present, defaults otherwise.
     * Includes 'items' when the section has a list.
     */
    public static function get(string $key): array
    {
        $def = self::definition($key);
        if (! $def) {
            return [];
        }
        $cacheKey = 'site.page_content.' . $key;
        if (app()->bound($cacheKey)) {
            return app($cacheKey);
        }
        $data = self::saved()[$key] ?? [];
        $values = [];

        foreach ($def['fields'] as $name => $field) {
            $value = array_key_exists($name, $data) ? $data[$name] : ($field['default'] ?? null);
            if (is_string($value) && str_starts_with($value, 'view:')) {
                $value = trim(view(substr($value, 5))->render());
                // drop the Blade comment line the default views start with
                $value = trim(preg_replace('/^\s*<!--.*?-->\s*/s', '', $value));
            }
            $values[$name] = ($field['type'] ?? '') === 'toggle' ? (bool) $value : $value;
        }

        if (isset($def['items'])) {
            $values['items'] = array_key_exists('items', $data) ? ($data['items'] ?? []) : $def['items']['default'];
        }

        app()->instance($cacheKey, $values);

        return $values;
    }
}
