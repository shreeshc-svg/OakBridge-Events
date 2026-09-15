@extends('frontend.layouts.app')
@section('title', 'Speaker' . ' ' . '-' . ' ' . $speaker->name)
@section('keywords', $setting->site_keywords)
@section('description', $setting->site_description)
@section('content')
    <!--Page Title-->

    <section class="page-title" style="background-image:url({{ asset('public/assets/images/background/5.jpg') }});">
        <section class="page-title"
            style="background-image:linear-gradient(rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.75)), url({{ asset('public/assets/images/background/bread2.webp') }}); background-size: cover; background-position: center;">

            <div class="auto-container">

                <h1>{{ $speaker->name }}</h1>

                <ul class="bread-crumb clearfix">

                    <li><a href="{{ route('home') }}">Home</a></li>

                    <li>Speakers</li>

                </ul>

            </div>

        </section>

        <!--End Page Title-->



        <section class="speaker-detail">

            <div class="auto-container">

                <div class="row">

                    <div class="image-column col-lg-4 col-md-12 col-sm-12">

                        <div class="image-box">

                            <figure class="image"><img src="{{ asset('public/uploads/images/team/' . $speaker->image) }}"
                                    alt=""></figure>

                            <ul class="social-icon-two social-icon-colored text-center">

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

                                {{-- <li><a href="#"><span class="fab fa-google-plus-g"></span></a></li>

                            <li><a href="#"><span class="fab fa-twitter"></span></a></li>

                            <li><a href="#"><span class="fab fa-skype"></span></a></li>

                            <li><a href="#"><span class="fab fa-linkedin-in"></span></a></li> --}}

                            </ul>

                        </div>

                    </div>



                    <div class="info-column col-lg-8 col-md-12 col-sm-12">

                        <div class="inner-column">

                            <div class="text-box">

                                <h3>{{ $speaker->name }}</h3>

                                {!! $speaker->bio !!}

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>

    @stop
