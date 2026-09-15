@extends('frontend.layouts.app')
@section('title', 'About India Law, AI & Tech Summit 2025')
@section('keywords', 'legal tech, law ai, tech summit, legal tech summit, new technology, technology summit, legal tech leaders, india law ai, ai in law, ai legal tech, about ilats')
@section('description', 'The India Law AI Tech Summit 2025 envisions Indias premier annual forum for legal innovation. A dynamic experience designed for maximum engagement and unparalled access celebrating Law AI Tech pioneers leaders and innovators driving Law AI Tech revolution.')
@section('content')


    <section class="page-title"
        style="background-image: url({{ asset('public/assets/images/background/bread2.webp') }}); background-size: cover; background-position: center;">

        <div class="auto-container">

            <h1>About Us</h1>

            <ul class="bread-crumb clearfix">

                <li><a href="{{ route('home') }}">Home</a></li>

                <li>About Us</li>

            </ul>

        </div>

    </section>


    <!-- About Section -->

    @include('frontend.components.about')




   {{-- <!-- Features Section Two -->
    @include('frontend.components.why-choose')
    <!--End Features Section --> --}}



    <!-- Call to action -->

    {{-- <section class="call-to-action" style="background-image: url({{ asset('public/assets/images/background/11.jpg') }});">

        <div class="auto-container">

            <div class="content-box">

                <div class="text">Join Us at Vidhi Utsav 2025! </div>

                <h2>Discover the intersection of law, literature, and knowledge at Vidhi Utsav!</h2>

                <div class="btn-box">

                    <a href="#" data-toggle="modal" data-target="#exampleModal" class="theme-btn btn-style-one"><span
                            class="btn-title">Register
                            Now</span></a>

                </div>

            </div>

        </div>

    </section> --}}

    <!--End Call to action -->



    <!-- Event Info Section -->

    {{-- <section class="event-info-section">

        <div class="auto-container">

            <div class="row">

                <!-- Info Column -->

                <div class="info-column col-lg-6 col-md-12 col-sm-12 order-2">

                    <div class="inner-column">

                        <div class="sec-title style-two">

                            <span class="title">Reach us</span>

                            <h2>Direction for the <br>Event hall</h2>

                        </div>



                        <div class="event-info-tabs tabs-box">

                            <!--Tabs Box-->

                            <ul class="tab-buttons clearfix">

                                <li class="tab-btn active-btn" data-tab="#tab1">Time</li>

                                <li class="tab-btn" data-tab="#tab2">Venue</li>

                                <li class="tab-btn" data-tab="#tab3">How to</li>

                            </ul>



                            <div class="tabs-content">

                                <!--Tab-->

                                <div class="tab active-tab" id="tab1">

                                    <h4><span class="icon far fa-calendar"></span> 21 & 22(Fri-Sat) February 2025</h4>

                                    <div class="text">09:30 AM - 06:00 PM</div>

                                    <ul class="info-list">

                                        <li><span class="icon icon_phone"></span> <a
                                                href="tel:{{ $setting->phone }}">{{ $setting->phone }}</a></li>

                                        <li><span class="icon icon_mail"></span> <a
                                                href="mailto:{{ $setting->email }}">{{ $setting->email }}</a></li>

                                    </ul>

                                </div>



                                <!--Tab-->

                                <div class="tab" id="tab2">

                                    <h4><span class="icon fa fa-map-marker-alt"></span>{{ $setting->address }}</h4>


                                </div>



                                <!--Tab-->

                                <div class="tab" id="tab3">

                                    <h4><span class="icon fa fa-directions"></span> How to get there</h4>

                                    <p>
                                        <a class="btn btn-primary" href="https://maps.app.goo.gl/21boPJbsMyd4X1eH6"
                                            target="_blank">View Venue Location</a>
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>



                <!-- Image Column -->
                @if ($setting->map)
                    <div class="map-column col-lg-6 col-md-12 col-sm-12">

                        <!--Map Outer-->

                        <div class="map-outer">

                            <!--Map Canvas-->

                            {!! $setting->map !!}

                        </div>

                    </div>
                @endif

            </div>

        </div>

    </section> --}}

    <!--End Event Info Section -->


@stop

@section('css')


@stop
