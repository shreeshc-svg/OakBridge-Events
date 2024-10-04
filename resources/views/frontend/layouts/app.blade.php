<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="@yield('keywords')" name="keywords">
    <meta content="@yield('description')" name="description">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap"
        rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link href="{{ asset('public/assets/lib/animate/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/assets/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/assets/lib/lightbox/css/lightbox.min.css') }}" rel="stylesheet">


    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('public/assets/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="{{ asset('public/assets/css/style.css') }}" rel="stylesheet">
    @if ($setting->gtag)
        {!! $setting->gtag !!}
    @endif

    <style>
        .read-more-target,
.read-more-trigger_opened {
  display: none;
}
.read-more-state:checked ~ .read-more-wrap .read-more-target,
.read-more-state:checked ~ .read-more-trigger_opened {
  display: block;
}
.read-more-state:checked ~ .read-more-trigger_closed {
  display: none;
}
      </style>



</head>

<body>

    <!-- Topbar Start -->
    <div class="container-fluid topbar px-0 d-none d-lg-block">
        <div class="container px-0">
            <div class="row gx-0 align-items-center" style="height: 45px;">
                <div class="col-lg-8 text-center text-lg-start mb-lg-0">
                    <div class="d-flex flex-wrap">
                        <a href="#" class="text-muted me-4"><i
                                class="fas fa-map-marker-alt text-primary me-2"></i>{{ Str::limit($setting->address, 50) }}</a>
                        @if ($setting->phone)
                            <a href="#" class="text-muted me-4"><i
                                    class="fas fa-phone-alt text-primary me-2"></i>{{ str_replace(' ', '', $setting->phone) }}</a>
                        @endif
                        @if ($setting->email)
                            <a href="#" class="text-muted me-0"><i
                                    class="fas fa-envelope text-primary me-2"></i>{{ $setting->email }}</a>
                        @endif
                    </div>
                </div>
                <div class="col-lg-4 text-center text-lg-end">
                    <div class="d-flex align-items-center justify-content-end">
                        @if ($setting->facebook)
                            <a target="_blank" href="{{ $setting->facebook }}"
                                class="btn btn-primary btn-square rounded-circle nav-fill me-3"><i
                                    class="fab fa-facebook-f text-white"></i></a>
                        @endif
                        @if ($setting->twitter)
                            <a target="_blank" href="{{ $setting->twitter }}"
                                class="btn btn-primary btn-square rounded-circle nav-fill me-3"><i
                                    class="fab fa-twitter text-white"></i></a>
                        @endif
                        @if ($setting->instagram)
                            <a target="_blank" href="{{ $setting->instagram }}"
                                class="btn btn-primary btn-square rounded-circle nav-fill me-3"><i
                                    class="fab fa-instagram text-white"></i></a>
                        @endif
                        @if ($setting->linkedin)
                            <a target="_blank" href="{{ $setting->linkedin }}"
                                class="btn btn-primary btn-square rounded-circle nav-fill me-0"><i
                                    class="fab fa-linkedin-in text-white"></i></a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Topbar End -->


    <!-- Navbar & Hero Start -->
    <div class="container-fluid sticky-top px-0">
        <div class="position-absolute bg-dark" style="left: 0; top: 0; width: 100%; height: 100%;">
        </div>
        <div class="container px-0">
            <nav class="navbar navbar-expand-lg navbar-dark bg-white py-3 px-4">
                <a href="index.html" class="navbar-brand p-0">
                    <h1 class="text-primary m-0"></h1>
                    <img class="d-none d-sm-block" src="{{ asset('public/uploads/images/logo/' . $setting->logo) }}"
                        width="300" alt="Logo">

                    <img class="d-block d-sm-none" src="{{ asset('public/uploads/images/logo/' . $setting->logo) }}"
                        width="260" alt="Logo">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto py-0">
                        <a href="{{ route('home') }}"
                            class="nav-item nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
                        <a href="{{ route('about') }}"
                            class="nav-item nav-link {{ request()->routeIs('about') ? 'active' : '' }} ">About</a>
                        <div class="nav-item dropdown">
                            <a href="{{ route('service') }}" class="nav-link dropdown-toggle {{ request()->routeIs('service') ? 'active' : '' }}" data-bs-toggle="dropdown">Jobs</a>
                            <div class="dropdown-menu m-0">
                                @foreach ($service_categories as $category)
                                <a href="{{ route('service',$category->slug) }}" class="dropdown-item capitalize">{{ $category->title }}</a>
                                @endforeach
                                <a href="{{ route('service') }}" class="dropdown-item">View All</a>
                            </div>
                            </div>
                        <a href="{{ route('faq') }}"
                            class="nav-item nav-link {{ request()->routeIs('faq') ? 'active' : '' }} ">FAQ</a>
                        <a href="{{ route('blog') }}"
                            class="nav-item nav-link {{ request()->routeIs('blog') ? 'active' : '' }} ">Blogs</a>
                        <a href="{{ route('grievence') }}"
                            class="nav-item nav-link {{ request()->routeIs('grievence') ? 'active' : '' }} ">Grievence</a>

                        <a href="{{ route('contact') }}"
                            class="nav-item nav-link {{ request()->routeIs('contact') ? 'active' : '' }} ">Contact</a>
                    </div>
                    <div class="d-flex align-items-center flex-nowrap pt-xl-0">
                        <a href="{{ route('service') }}"
                            class="btn btn-primary rounded-pill text-white py-2 px-4 ms-2 flex-wrap flex-sm-shrink-0">Current
                            Jobs</a>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- Navbar & Hero End -->

    <!-- Modal Search Start -->
    <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content rounded-0">
                <div class="modal-header">
                    <h4 class="modal-title mb-0" id="exampleModalLabel">Search by keyword</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex align-items-center">
                    <div class="input-group w-75 mx-auto d-flex">
                        <input type="search" class="form-control p-3" placeholder="keywords"
                            aria-describedby="search-icon-1">
                        <span id="search-icon-1" class="input-group-text p-3"><i class="fa fa-search"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Search End -->




    @yield('content')
    <!-- Footer Start -->
    <div class="container-fluid footer py-5 wow fadeIn" data-wow-delay="0.2s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="footer-item d-flex flex-column">
                        <div class="footer-item">
                            <img class="img-fluid bg-white p-2 rounded-2"
                                src="{{ asset('public/uploads/images/logo/' . $setting->logo) }}" alt="Logo">
                            <p class="mb-3">At SKY LEAD TRAVELS PVT. LTD., we excel in matching top talent with
                                leading organizations.</p>
                            <!-- <div class="position-relative mx-auto rounded-pill">
                            <input class="form-control rounded-pill w-100 py-3 ps-4 pe-5" type="text" placeholder="Enter your email">
                            <button type="button" class="btn btn-primary rounded-pill position-absolute top-0 end-0 py-2 mt-2 me-2">SignUp</button>
                        </div> -->
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="footer-item d-flex flex-column">
                        <h4 class="text-white mb-4">Explore</h4>
                        <a href="{{ route('home') }}"><i class="fas fa-angle-right me-2"></i> Home</a>
                        <a href="{{ route('about') }}"><i class="fas fa-angle-right me-2"></i> About Us</a>
                        <a href="{{ route('service') }}"><i class="fas fa-angle-right me-2"></i> Jobs</a>
                        <a href="{{ route('faq') }}"><i class="fas fa-angle-right me-2"></i> FAQ</a>
                        <a href="{{ route('blog') }}"><i class="fas fa-angle-right me-2"></i> Blogs</a>
                        <a href="{{ route('grievence') }}"><i class="fas fa-angle-right me-2"></i> Grievence</a>
                        <a href="{{ route('contact') }}"><i class="fas fa-angle-right me-2"></i> Contact Us</a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="footer-item d-flex flex-column">
                        <h4 class="text-white mb-4">Jop</h4>
                        @foreach ($featured_products as $featured_product)
                            <a href="{{ route('service.detail', $featured_product->slug) }}"><i
                                    class="fas fa-angle-right me-2"></i>{{ $featured_product->title }}</a>
                        @endforeach
                    </div>
                </div>

                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="footer-item d-flex flex-column">
                        <h4 class="text-white mb-4">Contact Info</h4>
                        <a href=""><i class="fa fa-map-marker-alt me-2"></i>{{ $setting->address }}</a>

                        @if ($setting->email)
                            <a href="mailto:{{ $setting->email }}"><i
                                    class="fas fa-envelope me-2"></i>{{ $setting->email }}</a>
                        @endif

                        <a href="mailto:skyleadtravels@gmail.com"><i
                            class="fas fa-envelope me-2"></i>skyleadtravels@gmail.com</a>

                        @if ($setting->phone)
                            <a href="tel:{{ str_replace(' ', '', $setting->phone) }}"><i
                                    class="fas fa-phone me-2"></i>{{ $setting->phone }}</a>
                        @endif
                        @if ($setting->phone2)
                        <a href="tel:{{ str_replace(' ', '', $setting->phone2) }}"><i
                                class="fas fa-phone me-2"></i>{{ $setting->phone2 }}</a>
                                @endif

                                <a href="tel:01144777277"><i
                                        class="fas fa-phone me-2"></i>011-44777277</a>


                        <div class="d-flex align-items-center">
                            @if ($setting->facebook)
                                <a target="_blank" class="btn btn-light btn-md-square me-2"
                                    href="{{ $setting->facebook }}"><i class="fab fa-facebook-f"></i></a>
                            @endif
                            @if ($setting->twitter)
                                <a target="_blank" class="btn btn-light btn-md-square me-2"
                                    href="{{ $setting->twitter }}"><i class="fab fa-twitter"></i></a>
                            @endif
                            @if ($setting->instagram)
                                <a target="_blank" class="btn btn-light btn-md-square me-2"
                                    href="{{ $setting->instagram }}"><i class="fab fa-instagram"></i></a>
                            @endif
                            @if ($setting->linkedin)
                                <a target="_blank" class="btn btn-light btn-md-square me-0"
                                    href="{{ $setting->linkedin }}"><i class="fab fa-linkedin-in"></i></a>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- Footer End -->


    <!-- Copyright Start -->
    <div class="container-fluid copyright py-4 p-0">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-md-6 text-center text-md-start mb-md-0">
                  <span class="text-body"><a href="#" class="border-bottom text-primary"><i class="fas fa-copyright text-light me-2"></i>{{ $setting->bname }}</a>, All rights reserved.</span>

                </div>
                <div class="col-md-6 text-center text-md-end text-body">
                    <a target="_blank" class="border-bottom text-primary text-decoration-none"
                        href="https://www.vfixtechnology.com/">@ {{ date('Y') }} | Made with ❤️ by VFIX
                        TECHNOLOGY</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Copyright End -->




    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('public/assets/lib/wow/wow.min.js') }}"></script>
    <script src="{{ asset('public/assets/lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('public/assets/lib/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ asset('public/assets/lib/counterup/counterup.min.js') }}"></script>
    <script src="{{ asset('public/assets/lib/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('public/assets/lib/lightbox/js/lightbox.min.js') }}"></script>


    <!-- Template Javascript -->
    <script src="{{ asset('public/assets/js/main.js') }}"></script>
</body>


</html>


<style>
    .calling-button {
        position: fixed;
        bottom: 30%;
        right: 10px;
        z-index: 9999;
        width: 50px !important;
    }

    .fixed-buttonn {
        position: fixed;
        bottom: 10%;
        right: 8px;
        z-index: 9999;
    }

    .button-whatsapp {
        background: #fff;
        padding: 2px 7px;
        box-shadow: 0 0 13px rgb(0 0 0 / 65%);
        font-weight: 700;
        border-radius: 30px;
    }
</style>
<!-- calling sticky button -->
@if ($setting->phone)
    <div class="calling-button d-md-none d-block">
        <a target="_blank" href="tel:{{ str_replace(' ', '', $setting->phone) }}">
            <div class="">
                <img style="width:60px !important;" src="{{ asset('public/assets/call.gif') }}">
            </div>
        </a>
    </div>
@endif
<!-- whatsapp fixed button -->
@if ($setting->whatsapp)
    <div class="fixed-buttonn">
        <a target="_blank" href="https://wa.me/{{ $setting->whatsapp }}">
            <div class="button-whatsapp text-dark">
                <img src="{{ asset('public/assets/whatsapp.png') }}">
                Chat Now
            </div>
        </a>
    </div>
@endif
