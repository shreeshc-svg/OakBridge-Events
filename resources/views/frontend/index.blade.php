@extends('frontend.layouts.app')
@section('title', $setting->bname . ' ' . '-' . '' . $setting->site_title)
@section('keywords', $setting->site_keywords)
@section('description', $setting->site_description)
@section('content')
    <!-- Banner Section -->

    {{-- <section class="banner-section">

        <div class="banner-carousel owl-carousel owl-theme">

            <!-- Slide Item -->

            <div class="slide-item" style="background-image: url({{ asset('public/assets/images/main-slider/1.jpg') }});">

                <div class="auto-container">

                    <div class="content-box">

                        <span class="title">February 21, 2025</span>

                        <h2> Vidhi <br>Utsav 2025</h2>

                        <ul class="info-list">

                            <li><span class="icon fa fa-chair"></span> 1000 Seats</li>

                            <li><span class="icon fa fa-user-alt"></span> 50 SPEAKERS</li>

                            <li><span class="icon fa fa-map-marker-alt"></span> New Delhi</li>

                        </ul>

                        <div class="btn-box"><a href="#" data-toggle="modal" data-target="#exampleModal"
                                class="theme-btn btn-style-two"><span class="btn-title">Book
                                    Now</span></a></div>

                    </div>

                </div>

            </div>



            <!-- Slide Item -->

            <div class="slide-item" style="background-image: url({{ asset('public/assets/images/main-slider/2.jpg') }});">

                <div class="auto-container">

                    <div class="content-box">

                        <span class="title">February 22, 2025</span>

                        <h2> Vidhi <br>Utsav 2025</h2>

                        <ul class="info-list">

                            <li><span class="icon fa fa-chair"></span> 1000 Seats</li>

                            <li><span class="icon fa fa-user-alt"></span> 50 SPEAKERS</li>

                            <li><span class="icon fa fa-map-marker-alt"></span> New Delhi</li>

                        </ul>

                        <div class="btn-box"><a href="#" data-toggle="modal" data-target="#exampleModal"
                                class="theme-btn btn-style-two"><span class="btn-title">Book
                                    Now</span></a></div>

                    </div>

                </div>

            </div>

        </div>

    </section> --}}

    {{-- Hero banner: managed from Admin > Hero Banner --}}
    @php
        $heroDir = 'public/uploads/images/hero/';
        $heroEnabled = $setting->hero_enabled ?? true;
        $heroClick = $setting->hero_click ?? 'register';
        $heroImage = !empty($setting->hero_image)
            ? asset($heroDir . $setting->hero_image)
            : asset('public/assets/images/website_banner.webp');
        $heroMobile = !empty($setting->hero_image_mobile) ? asset($heroDir . $setting->hero_image_mobile) : null;
        $heroAlt = $setting->hero_alt ?? '';
    @endphp
    <section class="pt-5 pt-sm-0">
        @if ($heroEnabled)
            @if ($heroClick === 'link' && !empty($setting->hero_link))
                <a href="{{ $setting->hero_link }}" @if ($setting->hero_new_tab) target="_blank" rel="noopener" @endif>
            @endif
            <picture>
                @if ($heroMobile)
                    <source media="(max-width: 575.98px)" srcset="{{ $heroMobile }}">
                @endif
                <img class="pt-4 pt-sm-0 img-fluid w-100" src="{{ $heroImage }}" alt="{{ $heroAlt }}"
                    @if ($heroClick === 'register' && $registrationOpen) style="cursor: pointer" data-toggle="modal" data-target="#exampleModal" @endif>
            </picture>
            @if ($heroClick === 'link' && !empty($setting->hero_link))
                </a>
            @endif
        @endif
    </section>

    <!--End Banner Section -->

    @include('frontend.components.marketing-strip')



    <!-- Coming Soon -->

    <!--<section class="coming-soon-section">-->

    <!--    <div class="auto-container">-->

    <!--        <div class="outer-box">-->

    <!--            <div class="time-counter">-->
    <!--                <div class="time-countdown clearfix" data-countdown="2/21/2025 09:30:00"></div>-->
    <!--            </div>-->

    <!--        </div>-->

    <!--    </div>-->

    <!--</section>-->

    <!-- End Coming Soon -->


    <section class="py-5" style="background-color: #f8f9fa;">
        <div class="container">
            <div class="sec-title text-center">

                <span class="title">
                    Forging the Future of Law, AI & Tech
                </span>

                <!--<h2>Introduction</h2>-->
                <p style="color:#444444">
                    The India Law, AI & Tech Summit envisions India’s premier annual forum for legal innovation. A dynamic
                    experience designed for maximum engagement and unparalled access, celebrating Law, AI & Tech
                    pioneers, leaders, and innovators driving Law, AI & Tech revolution.
                </p>

            </div>
            <div class="row g-4">
                <div class="col-lg-6 col-md-6 mb-3">
                    <div class="card h-100  p-4 shadow-sm">
                        <div class=" text-center">
                            <div class="mb-3" style="font-size: 2.5rem; color: #b8860b;">
                                <i class="fa fa-medal"></i>
                            </div>
                            <h4 class="card-title mb-1" style="color: #333; font-weight: 600;">
                                Establishing India's Premier Law, AI & Tech Event
                            </h4>
                            <p class="card-text" style="color: #666666">
                                Creating the definitive annual event for Law, Technology and Innovation in the region.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-6 mb-3">
                    <div class="card h-100 p-4 shadow-sm">
                        <div class=" text-center">
                            <div class="mb-3" style="font-size: 2.5rem; color: #b8860b;">
                                <i class="fa fa-handshake"></i>
                            </div>
                            <h4 class="card-title mb-1" style="color: #333; font-weight: 600;">
                                Uniting Key Stakeholders
                            </h4>
                            <p class="card-text" style="color: #666666">
                                Bringing together Top Law Firms, General Counsels, Policymakers, Technologists, and
                                Innovators to foster collaboration.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-6 mb-3">
                    <div class="card h-100  p-4  shadow-sm">
                        <div class="y text-center">
                            <div class="mb-3" style="font-size: 2.5rem; color: #b8860b;">
                                <i class="fa fa-globe"></i>
                            </div>
                            <h4 class="card-title mb-1" style="color: #333; font-weight: 600;">
                                Shaping the Future of Law Roadmap
                            </h4>
                            <p class="card-text" style="color: #666666">
                                Influencing the adoption and evolution of AI & Tech solutions across India's legal
                                landscape.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-6 mb-3">
                    <div class="card h-100  p-4  shadow-sm">
                        <div class=" text-center">
                            <div class="mb-3" style="font-size: 2.5rem; color: #b8860b;">
                                <i class="fa fa-book"></i>
                            </div>
                            <h4 class="card-title mb-1" style="color: #333; font-weight: 600;">
                                Envisioning the 'Davos of Law, AI & Tech'
                            </h4>
                            <p class="card-text" style="color: #666666">
                                Becoming the essential annual gathering for Thought Leadership and Networking in Law, AI & Tech.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Features Section Two -->

    @include('frontend.components.why-choose')

    <!--End Features Section -->



    <section class="why-choose-us">

        <div class="auto-container">

            <div class="row align-items-center">

                <div class="content-column col-lg-6 col-md-12 col-sm-12 ">

                    <div class="inner-column">

                        <div class="sec-title">

                            <span class="title">A Curated Gathering of Legal Luminaries & Innovators</span>

                            <h2>Who Should Attend?</h2>
                            {{--
                            <div class="text">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmtempor
                                incididunt labore et dolore magna aliqu enim ad minim veniam quis nostrud exercitation
                                ullamco laboris nisi ut aliquip</div> --}}

                        </div>

                        <ul class="list-style-one">

                            <li>Managing Partners/Sr Partners of Top Law Firms</li>

                            <li>General Counsels of India's Top Corporates</li>

                            <li>CTOs/CIOs of Top Law Firms & Corporates</li>

                            <li>AI & Tech Solutions Providers</li>

                            <li>Regulators, Law and Policymakers, Judiciary</li>

                        </ul>
                        @if ($registrationOpen)
                        <div class="btn-box"><a href="#" data-toggle="modal" data-target="#exampleModal"
                                class="theme-btn btn-style-three"><span class="btn-title">Register Now</span></a></div>
                        @endif
                    </div>

                </div>

                <div class="image-column col-lg-6 col-md-12 col-sm-12 ">

                    <div class="image-box">

                        <figure class="image"><img src="{{ asset('public/assets/images/why.webp') }}" alt="">
                        </figure>

                    </div>

                </div>

            </div>

        </div>

    </section>



    <!-- Speakers Section -->

    <section class="speakers-section-three">

        <div class="auto-container">

            <div class="sec-title text-center">

                <span class="title">{{ $setting->bname }}</span>

                <h2>Distinguished Speakers</h2>

            </div>



            <div class="row">



                <!-- Speaker Block -->

                @foreach ($speakers as $speaker)
                    <div class="speaker-block-three col-xl-3 col-lg-4 col-md-6 col-sm-12 wow fadeInUp">

                        <div class="inner-box">

                            <div class="image-box">

                                <figure class="image"><a href="javascript::void"><img
                                            src="{{ asset('public/uploads/images/team/' . $speaker->image) }}"
                                            alt=""></a>
                                </figure>

                            </div>

                            <div class="info-box">

                                <h4 class="name"><a href="javascript::void">{{ $speaker->name }}</a></h4>

                                <span class="designation small">{{ $speaker->position }}</span>

                            </div>

                            <div class="social-box">

                                <ul class="social-links social-icon-colored">

                                    @if ($speaker->social['facebook'])
                                        <li><a target="_blank" href="{{ $speaker->social['facebook'] }}"><span
                                                    class="fab fa-facebook-f"></span></a>
                                        </li>
                                    @endif

                                    @if ($speaker->social['instagram'])
                                        <li><a target="_blank" href="{{ $speaker->social['instagram'] }}"><span
                                                    class="fab fa-instagram"></span></a>
                                        </li>
                                    @endif


                                    @if ($speaker->social['x'])
                                        <li><a target="_blank" href="{{ $speaker->social['x'] }}"><span
                                                    class="fab fa-twitter"></span></a>
                                        </li>
                                    @endif


                                    @if ($speaker->social['linkedin'])
                                        <li><a target="_blank" href="{{ $speaker->social['linkedin'] }}"><span
                                                    class="fab fa-linkedin-in"></span></a>
                                        </li>
                                    @endif

                                    @if ($speaker->social['youtube'])
                                        <li><a target="_blank" href="{{ $speaker->social['youtube'] }}"><span
                                                    class="fab fa-youtube"></span></a>
                                        </li>
                                    @endif

                                </ul>

                            </div>

                        </div>

                    </div>
                @endforeach


            </div>

            <div class="row pt-3">
                <div class="btn-box w-100">

                    <a href="{{ route('speakers') }}" class="theme-btn btn-style-three w-100"><span
                            class="btn-title">View
                            All</span></a>

                </div>

            </div>

        </div>

    </section>

    <!-- End Speakers Section -->




    <!-- Pricing Section -->

    @include('frontend.components.events')

    <!--End Pricing Section -->





    <!--Clients Section-->

    @include('frontend.components.sponsors')

    <!--End Clients Section-->



    <!-- Register Section -->
    @if ($registrationOpen)

    <section class="register-section">

        <div class="auto-container">

            <div class="anim-icons full-width">

                <span class="icon icon-circle-3 wow zoomIn"></span>

            </div>

            <div class="outer-box">

                <div class="row no-gutters">

                    <div class="title-column col-lg-4 col-md-6 col-sm-12">

                        <div class="inner">

                            <div class="sec-title light">

                                <div class="icon-box"><span class="">
                                        <img class="w-50"
                                            src="{{ asset('public/uploads/images/logo/' . $setting->logo) }}"
                                            alt=""></span></div>

                                <h2>Register Now</h2>

                                <div class="text">Be part of this vibrant summit that brings together experts and
                                    enthusiasts on Legal Tech and AI to discuss and ideate on the profound
                                    connections between law and tech.</div>

                            </div>

                        </div>

                    </div>

                    <!--Register Form-->

                    <div id="booking" class="register-form col-lg-8 col-md-6 col-sm-12">

                        <div class="form-inner">

                            @include('frontend.components.booking-form')

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    @endif
    <!--End Register Section -->





    <!--End News Section -->

@endsection



@section('js')

    {{-- popup trigger code here --}}
    <script>
        $(document).ready(function() {
            // Check if the user has already visited the "Thanks" page
            var formSubmitted = localStorage.getItem('form_submitted');

           {{-- if (!formSubmitted) {
                // Trigger the modal after 30 seconds
                setTimeout(function() {
                    $('#exampleModal').modal('show');
                }, 30000); // 30000 milliseconds = 30 seconds
            }  --}}

            // Store form submission status when the form is submitted
            $('form').on('submit', function() {
                localStorage.setItem('form_submitted', 'true');
            });
        });
    </script>
@stop
