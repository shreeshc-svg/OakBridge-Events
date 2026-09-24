<!DOCTYPE html>

<html lang="en">


<head>

    <meta charset="utf-8">

    @php
        $seoTitle = \App\Support\Seo::title(trim(html_entity_decode($__env->yieldContent('title'), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        $seoDescription = \App\Support\Seo::description(trim(html_entity_decode($__env->yieldContent('description'), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        $seoKeywords = \App\Support\Seo::keywords(trim(html_entity_decode($__env->yieldContent('keywords'), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
    @endphp
    <title>{{ $seoTitle }}</title>
    @if ($seoDescription)
        <meta name="description" content="{{ $seoDescription }}">
        <meta property="og:description" content="{{ $seoDescription }}">
    @endif
    @if ($seoKeywords)
        <meta name="keywords" content="{{ $seoKeywords }}">
    @endif
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">

    <!-- Stylesheets -->

    <link href="{{ asset('public/assets/css/bootstrap.css') }}" rel="stylesheet">

    <link href="{{ asset('public/assets/css/style.css') }}" rel="stylesheet">

    <link href="{{ asset('public/assets/css/responsive.css') }}" rel="stylesheet">

    <link href="{{ asset('public/assets/css/custom.css') }}?v=5" rel="stylesheet">

    <!--Color Switcher Mockup-->

    <link href="{{ asset('public/assets/css/color-switcher-design.css') }}" rel="stylesheet">



    <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">

    <link rel="icon" href="{{ asset('public/uploads/images/logo/' . $setting->logo) }}" type="image/png">



    <!-- Responsive -->

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">



    <!--[if lt IE 9]><script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js') }}"></script><![endif]-->

    <!--[if lt IE 9]><script src="{{ asset('public/assets/js/respond.js') }}"></script><![endif]-->

    @if ($setting->gtag)
        {!! $setting->gtag !!}
    @endif

</head>



<body>

    <!-- Registration modal (hidden when registration is closed in Admin > Registration) -->
    @if ($registrationOpen)
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Hurry! Register now.</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{--<small class="text-danger text-center pb-3">**Booking will be confirmed upon payment.</small>--}}
                    @include('frontend.components.booking-form')
                </div>
            </div>
        </div>
    </div>
    @endif



    <div class="page-wrapper">
        <!-- Main Header-->
        <!-- Header Span -->

        <span class="header-span"></span>



        <!-- Main Header-->

        <header class="main-header header-style-two">

            <div class="main-box">

                <div class="auto-container clearfix">

                    <div class="logo-box">

                        <div class="logo "><a href="{{ route('home') }}"><img class="rounded"
                                    src="{{ asset('public/uploads/images/logo/' . $setting->logo) }}" alt=""
                                    title="" width="80"></a></div>

                    </div>



                    <!--Nav Box-->

                    <div class="nav-outer clearfix">

                        <!--Mobile Navigation Toggler-->

                        <div class="mobile-nav-toggler"><span class="icon flaticon-menu"></span></div>

                        <!-- Main Menu -->

                        <nav class="main-menu navbar-expand-md navbar-light">

                            <div class="navbar-header">

                                <!-- Togg le Button -->

                                <button class="navbar-toggler" type="button" data-toggle="collapse"
                                    data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                                    aria-expanded="false" aria-label="Toggle navigation">

                                    <span class="icon flaticon-menu-button"></span>

                                </button>

                            </div>



                            <div class="collapse navbar-collapse clearfix" id="navbarSupportedContent">

                                <ul class="navigation clearfix">
                                    {{-- links are managed in Admin > Menus --}}
                                    @foreach (\App\Support\SiteMenu::items('header') as $menuItem)
                                        <li class="{{ count($menuItem['children']) ? 'dropdown' : '' }} {{ \App\Support\SiteMenu::isCurrent($menuItem) ? 'current' : '' }}">
                                            <a href="{{ \App\Support\SiteMenu::href($menuItem['url']) }}"
                                                @if ($menuItem['new_tab']) target="_blank" rel="noopener" @endif>{{ $menuItem['label'] }}</a>
                                            @if (count($menuItem['children']))
                                                <ul>
                                                    @foreach ($menuItem['children'] as $subItem)
                                                        <li><a href="{{ \App\Support\SiteMenu::href($subItem['url']) }}"
                                                                @if ($subItem['new_tab']) target="_blank" rel="noopener" @endif>{{ $subItem['label'] }}</a></li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </li>
                                    @endforeach

                                    @if ($registrationOpen)
                                    <div class="btn-box d-block d-sm-none mt-4 ml-3">

                                        <a href="#" data-toggle="modal" data-target="#exampleModal"
                                            class="theme-btn btn-style-one"><span class="btn-title">{{ $registrationLabel }}</span></a>

                                    </div>
                                    @endif


                                </ul>

                            </div>

                        </nav>

                        <!-- Main Menu End-->



                        <!-- Outer box -->

                        <div class="outer-box">

                            <!--Search Box-->


                            <!-- Button Box -->

                            @if ($registrationOpen)
                            <div class="btn-box">
                                <a href="#" data-toggle="modal" data-target="#exampleModal"
                                    class="theme-btn btn-style-one"><span class="btn-title">{{ $registrationLabel }}</span></a>

                            </div>
                            @endif

                        </div>

                    </div>

                </div>

            </div>



            <!-- Mobile Menu  -->

            <div class="mobile-menu">

                <div class="menu-backdrop"></div>

                <div class="close-btn"><span class="icon flaticon-cancel-1"></span></div>



                <!--Here Menu Will Come Automatically Via Javascript / Same Menu as in Header-->

                <nav class="menu-box">

                    <div class="nav-logo"><a href="{{ route('home') }}"><img src="images/logo-2.png" alt=""
                                title=""></a></div>



                    <ul class="navigation clearfix"><!--Keep This Empty / Menu will come through Javascript--></ul>

                </nav>

            </div><!-- End Mobile Menu -->

        </header>

        <!--End Main Header -->



        @yield('content')


        <!-- Main Footer -->

        @php $footerContent = \App\Support\PageContent::get('footer'); @endphp
        <footer class="main-footer">

            <!--Widgets Section-->

            <div class="widgets-section">

                <div class="auto-container">

                    <div class="row">

                        <!--Big Column-->

                        <div class="big-column col-md-8">

                            <div class="row">

                                <!--Footer Column-->

                                <div class="footer-column col-xl-7 col-lg-6 col-md-6 col-sm-12">

                                    <div class="footer-widget about-widget">

                                        <div class="logo">

                                            <a href="{{ route('home') }}"><img src="images/logo.png"
                                                    alt="" /></a>

                                        </div>

                                        <div class="text">

                                            <p>{!! nl2br(e($footerContent['body'])) !!}</p>

                                        </div>

                                        <ul class="social-icon-one social-icon-colored">

                                            @if ($setting->facebook)
                                                <li><a target="_blank" href="{{ $setting->facebook }}"><i
                                                            class="fab fa-facebook-f"></i></a>
                                                    </li>
                                            @endif

                                            @if ($setting->twitter)
                                                <li><a target="_blank" href="{{ $setting->twitter }}"><i
                                                            class="fab fa-twitter"></i></a></li>
                                            @endif

                                            @if ($setting->instagram)
                                                <li><a target="_blank" href="{{ $setting->instagram }}"><i
                                                            class="fab fa-instagram"></i></a></li>
                                            @endif


                                            @if ($setting->linkedin)
                                                <li><a target="_blank" href="{{ $setting->linkedin }}"><i
                                                            class="fab fa-linkedin-in"></i></a></li>
                                            @endif


                                            @if ($setting->youtube)
                                                <li><a target="_blank" href="{{ $setting->youtube }}"><i
                                                            class="fab fa-youtube"></i></a></li>
                                            @endif


                                        </ul>

                                    </div>

                                </div>



                                <!--Footer Column-->

                                <div class="footer-column col-xl-5 col-lg-6 col-md-6 col-sm-12">

                                    <div class="footer-widget useful-links">

                                        <h2 class="widget-title">{{ $footerContent['links_title'] }}</h2>

                                        <ul class="user-links">
                                            @foreach (\App\Support\SiteMenu::items('footer') as $footerLink)
                                                <li><a href="{{ \App\Support\SiteMenu::href($footerLink['url']) }}"
                                                        @if ($footerLink['new_tab']) target="_blank" rel="noopener" @endif>{{ $footerLink['label'] }}</a></li>
                                            @endforeach
                                        </ul>

                                    </div>

                                </div>

                            </div>

                        </div>



                        <!--Big Column-->

                        <div class="big-column col-md-4">

                            <div class="row">

                                <!--Footer Column-->

                                <div class="footer-column col-md-12">

                                    <!--Footer Column-->

                                    <div class="footer-widget contact-widget">

                                        <h2 class="widget-title">{{ $footerContent['contact_title'] }}</h2>

                                        <!--Footer Column-->

                                        <div class="widget-content">

                                            <ul class="contact-list">




                                                @if ($setting->phone)
                                                    <li>

                                                        <span class="icon flaticon-phone"></span>


                                                        <div class="text"><a
                                                                href="tel:{{ $setting->phone }}">{{ $setting->phone }}</a>
                                                        </div>


                                                    </li>
                                                @endif

                                                @if ($setting->phone2)
                                                    <li>

                                                        <span class="icon flaticon-phone"></span>


                                                        <div class="text"><a
                                                                href="tel:{{ $setting->phone2 }}">{{ $setting->phone2 }}</a>
                                                        </div>


                                                    </li>
                                                @endif





                                                @if ($setting->email)
                                                    <li>

                                                        <span class="icon flaticon-paper-plane"></span>

                                                        <div class="text"><a
                                                                href="mailto:{{ $setting->email }}">{{ $setting->email }}</a>
                                                        </div>

                                                    </li>
                                                @endif



                                                @if ($setting->address)
                                                    <li>

                                                        <span class="icon flaticon-worldwide"></span>

                                                        <div class="text">{{ $setting->address }} </div>

                                                    </li>
                                                @endif


                                               {{-- <li>

                                                    <span class="icon flaticon-map"></span>




                                                   <div class="text"><a target="_blank"
                                                            href="https://maps.app.goo.gl/8K5s7p6bWZtNJcJQ6">View
                                                            Map</a>
                                                    </div>  


                                                </li> --}}


                                            </ul>

                                        </div>

                                    </div>

                                </div>


                            </div>

                        </div>

                    </div>

                </div>

            </div>



            <!--Footer Bottom-->

            <div class="footer-bottom">

                <div class="auto-container">

                    <div class="inner-container clearfix">

                        <div class="copyright-text">
                                <p>{{ $footerContent['copyright'] }}</p>
                           {{-- <p>@{{ date('Y') }} Made with ❤️ By <a target="_blank"
                                    href="https://www.vfixtechnology.com">VFIX
                                    TECHNOLOGY</a></p>  --}}

                        </div>

                    </div>

                </div>

            </div>

        </footer>

        <!-- End Footer -->



    </div>

    <!--End pagewrapper-->





    <!--Search Popup-->

    {{-- <div id="search-popup" class="search-popup">

        <div class="close-search theme-btn"><span class="fas fa-window-close"></span></div>

        <div class="popup-inner">

            <div class="overlay-layer"></div>

            <div class="search-form">

                <form method="post" action="https://expert-themes.com/html/eventrox/{{ route('home') }}">

                    <div class="form-group">

                        <fieldset>

                            <input type="search" class="form-control" name="search-input" value=""
                                placeholder="Search Here" required>

                            <input type="submit" value="Search Now!" class="theme-btn">

                        </fieldset>

                    </div>

                </form>



                <br>

                <h3>Recent Search Keywords</h3>

                <ul class="recent-searches">

                    <li><a href="#">Seo</a></li>

                    <li><a href="#">Bussiness</a></li>

                    <li><a href="#">Events</a></li>

                    <li><a href="#">Digital</a></li>

                    <li><a href="#">Conferance</a></li>

                </ul>



            </div>



        </div>

    </div> --}}



    <!--Scroll to top-->

    <div class="scroll-to-top scroll-to-target" data-target="html"><span class="fa fa-angle-double-up"></span>
    </div>

    <script src="{{ asset('public/assets/js/jquery.js') }}"></script>

    <script src="{{ asset('public/assets/js/popper.min.js') }}"></script>

    <script src="{{ asset('public/assets/js/bootstrap.min.js') }}"></script>

    <script src="{{ asset('public/assets/js/jquery-ui.js') }}"></script>

    <script src="{{ asset('public/assets/js/jquery.fancybox.js') }}"></script>

    <script src="{{ asset('public/assets/js/appear.js') }}"></script>

    <script src="{{ asset('public/assets/js/owl.js') }}"></script>

    <script src="{{ asset('public/assets/js/jquery.countdown.js') }}"></script>

    <script src="{{ asset('public/assets/js/wow.js') }}"></script>

    <script src="{{ asset('public/assets/js/script.js') }}"></script>

    <!-- Color Setting -->

    <script src="{{ asset('public/assets/js/color-settings.js') }}"></script>

    @yield('js')

    @if (
        $registrationOpen && (
        $errors->has('name') ||
            $errors->has('email') ||
            $errors->has('phone') ||
            $errors->has('designation') ||
            $errors->has('company') ||
            $errors->has('registration_closed') ||
            $errors->has('event') ||
            $errors->has('date') ||
            $errors->hasAny(['pass_type_id', 'quantity', 'billing_address', 'billing_state', 'billing_pin', 'buyer_gstin']) ||
            collect($errors->keys())->contains(fn ($key) => str_starts_with($key, 'attendees.'))))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Use Bootstrap's modal API to open the modal
                var modal = new bootstrap.Modal(document.getElementById('exampleModal'));
                modal.show(); 
            });
        </script>
    @endif



</body>

</html>
