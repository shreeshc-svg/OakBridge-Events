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

    @include('frontend.components.hero-banner')

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


    {{-- Intro: Admin > Page Content > Homepage > Intro and highlight cards --}}
    @if (\App\Support\SiteSections::isVisible('home.intro'))
    @php $intro = \App\Support\PageContent::get('home.intro'); @endphp
    <section class="py-5" style="background-color: #f8f9fa;">
        <div class="container">
            <div class="sec-title text-center">
                @if ($intro['eyebrow'])
                    <span class="title">{{ $intro['eyebrow'] }}</span>
                @endif
                @if ($intro['body'])
                    <div class="page-rich-text" style="color:#444444">{!! $intro['body'] !!}</div>
                @endif
            </div>
            @if (count($intro['items']))
                <div class="row g-4">
                    @foreach ($intro['items'] as $card)
                        <div class="col-lg-6 col-md-6 mb-3">
                            <div class="card h-100 p-4 shadow-sm">
                                <div class="text-center">
                                    @if ($card['icon'])
                                        <div class="mb-3" style="font-size: 2.5rem; color: #b8860b;">
                                            <i class="{{ $card['icon'] }}"></i>
                                        </div>
                                    @endif
                                    <h4 class="card-title mb-1" style="color: #333; font-weight: 600;">{{ $card['title'] }}</h4>
                                    <p class="card-text" style="color: #666666">{{ $card['text'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
    @endif

    <!-- Features Section Two -->

    @if (\App\Support\SiteSections::isVisible('home.overview'))
    @include('frontend.components.why-choose')
    @endif

    <!--End Features Section -->



    {{-- Who should attend: Admin > Page Content > Homepage --}}
    @if (\App\Support\SiteSections::isVisible('home.audience'))
    @php $audience = \App\Support\PageContent::get('home.audience'); @endphp
    <section class="why-choose-us">
        <div class="auto-container">
            <div class="row align-items-center">
                <div class="content-column col-lg-6 col-md-12 col-sm-12 ">
                    <div class="inner-column">
                        <div class="sec-title">
                            @if ($audience['eyebrow'])
                                <span class="title">{{ $audience['eyebrow'] }}</span>
                            @endif
                            <h2>{{ $audience['heading'] }}</h2>
                        </div>
                        @if (count($audience['items']))
                            <ul class="list-style-one">
                                @foreach ($audience['items'] as $line)
                                    <li>{{ $line['text'] }}</li>
                                @endforeach
                            </ul>
                        @endif
                        @if ($registrationOpen && $audience['button_label'])
                            <div class="btn-box"><a href="#" data-toggle="modal" data-target="#exampleModal"
                                    class="theme-btn btn-style-three"><span class="btn-title">{{ $audience['button_label'] }}</span></a></div>
                        @endif
                    </div>
                </div>
                @if ($audience['image'])
                    <div class="image-column col-lg-6 col-md-12 col-sm-12 ">
                        <div class="image-box">
                            <figure class="image"><img src="{{ \App\Support\Uploads::url($audience['image']) }}" alt="{{ $audience['heading'] }}">
                            </figure>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
    @endif

    <!-- Speakers Section -->

    @if (\App\Support\SiteSections::isVisible('home.speakers'))
    @php $speakersSection = \App\Support\PageContent::get('home.speakers'); @endphp
    <section class="speakers-section-three">

        <div class="auto-container">

            <div class="sec-title text-center">

                <span class="title">{{ $speakersSection['eyebrow'] ?: $setting->bname }}</span>

                <h2>{{ $speakersSection['heading'] }}</h2>

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

                                <h4 class="name" title="{{ $speaker->name }}"><a href="javascript::void">{{ $speaker->name }}</a></h4>

                                <span class="designation small" title="{{ $speaker->position }}">{{ $speaker->position }}</span>

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
                            class="btn-title">{{ $speakersSection['button_label'] ?: 'View All' }}</span></a>

                </div>

            </div>

        </div>

    </section>
    @endif

    <!-- End Speakers Section -->




    <!-- Pricing Section -->

    @if (\App\Support\SiteSections::isVisible('home.programme'))
    @include('frontend.components.events')
    @endif

    <!--End Pricing Section -->





    <!--Clients Section-->

    @if (\App\Support\SiteSections::isVisible('home.sponsors'))
    @include('frontend.components.sponsors')
    @endif

    <!--End Clients Section-->



    <!-- Register Section -->
    @if ($registrationOpen && \App\Support\SiteSections::isVisible('home.register'))

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

                                @php $registerSection = \App\Support\PageContent::get('home.register'); @endphp
                                <h2>{{ $registerSection['heading'] }}</h2>

                                <div class="text">{!! nl2br(e($registerSection['body'])) !!}</div>

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

    {{-- Marketing popup (Admin > Marketing Popup) --}}
    @include('frontend.components.promo-popup')

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
