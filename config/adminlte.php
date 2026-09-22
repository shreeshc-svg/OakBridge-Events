<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For detailed instructions you can look the title section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'title' => 'Admin Panel',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For detailed instructions you can look the favicon section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_ico_only' => false,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Google Fonts
    |--------------------------------------------------------------------------
    |
    | Here you can allow or not the use of external google fonts. Disabling the
    | google fonts may be useful if your admin panel internet access is
    | restricted somehow.
    |
    | For detailed instructions you can look the google fonts section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'google_fonts' => [
        'allowed' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For detailed instructions you can look the logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'logo' => '<strong>' . env('APP_NAME') . '</strong>',
    'logo_img' => '',
    //'logo_img' => 'public/vendor/adminlte/dist/img/AdminLTELogo.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => '',
    //'logo_img_alt' => 'Admin Logo',

    /*
    |--------------------------------------------------------------------------
    | Authentication Logo
    |--------------------------------------------------------------------------
    |
    | Here you can setup an alternative logo to use on your login and register
    | screens. When disabled, the admin panel logo will be used instead.
    |
    | For detailed instructions you can look the auth logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'auth_logo' => [
        'enabled' => false,
        'img' => [
            'path' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
            'alt' => 'Auth Logo',
            'class' => '',
            'width' => 50,
            'height' => 50,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Preloader Animation
    |--------------------------------------------------------------------------
    |
    | Here you can change the preloader animation configuration.
    |
    | For detailed instructions you can look the preloader section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'preloader' => [
        'enabled' => false,
        'img' => [
            'path' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
            'alt' => 'AdminLTE Preloader Image',
            'effect' => 'animation__shake',
            'width' => 60,
            'height' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For detailed instructions you can look the user menu section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'usermenu_enabled' => true,
    'usermenu_header' => false,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => false,
    'usermenu_desc' => false,
    'usermenu_profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look the layout section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For detailed instructions you can look the auth classes section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions you can look the admin panel classes here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For detailed instructions you can look the sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For detailed instructions you can look the right sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions you can look the urls section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_route_url' => true,
    'dashboard_url' => 'dashboard',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password.request',
    'password_email_url' => 'password.email',
    'profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Mix
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Mix option for the admin panel.
    |
    | For detailed instructions you can look the laravel mix section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'enabled_laravel_mix' => false,
    'laravel_mix_css_path' => 'css/app.css',
    'laravel_mix_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'menu' => [
        // Navbar items:
        [
            'type'         => 'navbar-search',
            'text'         => 'search',
            'topnav_right' => false,
        ],
        [
            'type'         => 'fullscreen-widget',
            'topnav_right' => false,
        ],

        // Sidebar items:
        [
            'type' => 'sidebar-menu-search',
            'text' => 'search',
        ],
        [
            'text' => 'blog',
            'url'  => 'admin/blog',
            'can'  => 'manage-blog',
        ],
        // [
        //     'text'        => 'pages',
        //     'url'         => 'admin/pages',
        //     'icon'        => 'far fa-fw fa-file',
        //     'label'       => 4,
        //     'label_color' => 'success',
        // ],
        // ['header' => 'account_settings'],

        //
        // [
        //     'text' => 'change_password',
        //     'url'  => 'admin/settings',
        //     'icon' => 'fas fa-fw fa-lock',
        // ],
        [
            'text' => 'Dashboard',
            'route'  => 'dashboard',
            'icon'    => 'fas fa-fw fa-tachometer-alt',
            // 'can'  => 'manage-blog',
        ],
        [
            'text' => 'Registration',
            'route'  => 'registration.edit',
            'icon' => 'fas fa-fw fa-user-check',
        ],
        [
            'text' => 'Orders',
            'url' => 'admin/orders',
            'icon' => 'fas fa-fw fa-receipt',
        ],
        [
            'text' => 'Ticketing',
            'url' => 'admin/ticketing',
            'icon' => 'fas fa-fw fa-tags',
        ],
        [
            'text' => 'Booking',
            'route'  => 'booking.index',
            'icon' => 'fas fa-ticket-alt',
        ],
        [
            'text'    => 'Posts',
            'url'  => 'admin/post/*',
            'icon'    => 'fas fa-fw fa-book',
            'submenu' => [
                [
                    'text' => 'Create Post',
                    'icon'    => 'fas fa-fw fa-plus',
                    'route'  => 'post.create',
                ],
                [
                    'text' => 'View All',
                    'icon'    => 'fas fa-fw fa-eye',
                    'route'  => 'post.index',
                ],
                [
                    'text' => 'View Trash',
                    'icon'    => 'fas fa-fw fa-trash',
                    'route'  => 'post.trash',
                ],

            ],
        ],
        [
            'text' => 'Post Catgories',
            'route'  => 'category.index',
            'icon' => 'fas fa-folder',
        ],
        [
            'text' => 'Post Tags',
            'route'  => 'tag.index',
            'icon' => 'fas fa-tags',
        ],
        [
            'text'    => 'Events',
            'url'  => '["admin/service/*","admin/scategory/*"]',
            'icon'    => 'fas fa-fw fa-shopping-bag',
            'submenu' => [
                // [
                //     'text' => 'Product Categories',
                //     'route'  => 'scategory.index',
                //     'icon' => 'fas fa-folder',
                // ],
                [
                    'text' => 'Add New',
                    'icon'    => 'fas fa-fw fa-plus',
                    'route'  => 'service.create',
                ],
                [
                    'text' => 'View All',
                    'icon'    => 'fas fa-fw fa-eye',
                    'route'  => 'service.index',
                ],
                [
                    'text' => 'Schedules',
                    'icon'    => 'fas fa-fw fa-calendar-alt',
                    'route'  => 'schedules.index',
                    'active' => ['admin/schedules*'],
                ],

            ],
        ],
        [
            'text' => 'Gallery',
            'route' => 'gallery.update',
            'icon' => 'far fa-images'
        ],
        // [
        //     'text' => 'Vidhi Samman',
        //     'route' => 'vidhi.index',
        //     'icon' => 'far fa-image'
        // ],
        [
            'text' => 'Videos',
            'route' => 'video.index',
            'icon' => 'fa fa-video'
        ],
        // [
        //     'text'    => 'Faq',
        //     'url'  => 'admin/faq/*',
        //     'icon'    => 'fas fa-question',
        //     'submenu' => [
        //         [
        //             'text' => 'Create faq',
        //             'icon'    => 'fas fa-fw fa-plus',
        //             'route'  => 'faq.create',
        //         ],
        //         [
        //             'text' => 'View All',
        //             'icon'    => 'fas fa-fw fa-eye',
        //             'route'  => 'faq.index',
        //         ],
        //     ]

        // ],
        [
            'text'    => 'Speakers / Advisors / Team',
            'url'  => 'admin/team/*',
            'icon'    => 'fas fa-users',
            'submenu' => [
                [
                    'text' => 'Add New',
                    'icon'    => 'fas fa-fw fa-plus',
                    'route'  => 'team.create',
                ],
                [
                    'text' => 'View All',
                    'icon'    => 'fas fa-fw fa-eye',
                    'route'  => 'team.index',
                ],


            ],
        ],
        [
            'text' => 'Testimonials',
            'url' => 'admin/testimonial/*',
            'icon' => 'fas fa-star',
            'submenu' => [
                [
                    'text' => 'Create testimonial',
                    'icon'    => 'fas fa-fw fa-plus',
                    'route'  => 'testimonial.create',
                ],
                [
                    'text' => 'View All',
                    'icon'    => 'fas fa-fw fa-eye',
                    'route'  => 'testimonial.index',
                ],


            ],
        ],
        // [
        //     'text' => 'Team',
        //     'url'  => 'admin/team/*',
        //     'icon'  => 'fas fa-users',
        //     'submenu' => [
        //         [
        //             'text' => 'Create Team',
        //             'icon' => 'fas fa-fw fa-plus',
        //             'route' => 'team.create',
        //         ],
        //         [
        //             'text' => 'View All',
        //             'icon' => 'fas fa-fw fa-eye',
        //             'route' => 'team.index',
        //         ],
        //     ]
        // ],
        ['header' => 'WEBSITE CONTENT'],
        [
            'text' => 'Hero Banner',
            'route'  => 'hero.edit',
            'icon' => 'fas fa-fw fa-image',
        ],
        [
            'text' => 'Marketing Strip',
            'route'  => 'strip.edit',
            'icon' => 'fas fa-fw fa-bullhorn',
        ],
        [
            'text' => 'Marketing Popup',
            'route'  => 'promo.edit',
            'icon' => 'fas fa-fw fa-rectangle-ad',
        ],
        [
            'text' => 'Page Content',
            'route'  => 'page-content.index',
            'icon' => 'fas fa-fw fa-file-alt',
            'active' => ['admin/page-content*'],
        ],
        [
            'text' => 'Sponsors & Exhibitors',
            'route'  => 'sponsors.index',
            'icon' => 'fas fa-fw fa-handshake',
        ],
        [
            'text' => 'Legathon',
            'route'  => 'competitions.index',
            'icon' => 'fas fa-fw fa-trophy',
            'active' => ['admin/competitions*'],
        ],
        [
            'text' => 'Menus',
            'route'  => 'menus.index',
            'icon' => 'fas fa-fw fa-bars',
        ],
        [
            'text' => 'SEO',
            'route'  => 'seo.index',
            'icon' => 'fas fa-fw fa-search',
        ],
        ['header' => 'ACCOUNT'],
        [
            'text' => 'profile',
            'route'  => 'profile',
            'icon' => 'fas fa-fw fa-user',
        ],
        [
            'text' => 'Settings',
            'route'  => 'setting',
            'icon' => 'fas fa-fw fa-cog',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For detailed instructions you can look the plugins section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Plugins-Configuration
    |
    */

    'plugins' => [
        'Datatables' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame
    |--------------------------------------------------------------------------
    |
    | Here we change the IFrame mode configuration. Note these changes will
    | only apply to the view that extends and enable the IFrame mode.
    |
    | For detailed instructions you can look the iframe mode section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/IFrame-Mode-Configuration
    |
    */

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'livewire' => false,
];
