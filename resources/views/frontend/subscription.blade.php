@extends('frontend.layouts.app')
@section('title', 'Benefits' . ' ' . '-' . '' . $setting->site_title)
@section('keywords', $setting->site_keywords)
@section('description', $setting->site_description)
@section('content')

    <!--====== Start Breadcrumb Section ======-->
    <section class="page-banner bg_cover p-r z-1 text-white"
        style="background-image: url(public/assets/images/bg/breadcrumbs-bg.jpg);">
        <div class="container">
            <h1 class="page-title">Subscription</h1>
            <div class="page-breadcrumb">
                <ul class="breadcrumb-link">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li class="active">Subscription</li>
                </ul>
            </div>
        </div>
    </section>
    <!--====== End Breadcrumb Section ======-->

    <!--====== Start Pricing Section ======-->
    <section class="about-section-shape pt-100 pb-70 p-r z-1">
        <div class="shape shape-one"><span><img src="{{ asset('public/assets/images/shape/shape-1.png') }}"
                    alt=""></span></div>
        <div class="shape shape-two"><span><img src="{{ asset('public/assets/images/shape/shape-2.png') }}"
                    alt=""></span></div>
        <div class="container">
            <div class="row">
                <div class="col-lg-5">
                    <div class="about-two_img-box mb-50">
                        <img src="{{ asset('public/assets/images/about/img-3.jpg') }}" class="about-img-one wow fadeInLeft"
                            alt="About Image">
                        <img src="{{ asset('public/assets/images/about/img-4.jpg') }}" class="about-img-two wow fadeInDown"
                            alt="About Image">
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="about-two_content-box content-box-gap wow fadeInRight mb-50">
                        <div class="section-title mb-30">
                            <span class="sub-title"><span class="line line1"></span>Time Is Money</span>
                            <h2>Get Ahead Of The Queue And Save Your Time</h2>
                        </div>
                        <p>Our subscription plan is a comprehensive offering that ensures your vehicle stays in optimal
                            condition without the hassle of service appointments. Following are the plans</p>
                        <div class="row">
                            <div class="col-xl-4 col-lg-12 col-md-6">
                                <div class="single-counter-item-two mb-40">
                                    <div class="text">
                                        <h2 class="number">₹<span class="">84</span></h2>
                                        <h4>1 month</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-12 col-md-6">
                                <div class="single-counter-item-two mb-40">
                                    <div class="text">
                                        <h2 class="number">₹<span class="">354</span></h2>
                                        <h4>6 Months</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-12 col-md-6">
                                <div class="single-counter-item-two mb-40">
                                    <div class="text">
                                        <h2 class="number">₹<span class="">578</span></h2>
                                        <h4>12 Months</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h4>Features</h4>
                        <ul class="check-style-one check-style-50 mb-30">
                            <li>Unlimited free pickup and drop</li>
                            <li>Priority customer support</li>
                            <li>Extra discounts on services</li>
                            <li>Priority Repairs</li>
                        </ul>
                        <div class="about-button">
                            <a href="{{ route('contact') }}#con" class="main-btn">Book Now</a>
                            <div class="call-button-box">
                                <div class="icon">
                                    <i class="flaticon-24-hours"></i>
                                </div>
                                <div class="text">
                                    <span>Free support</span>
                                    <h5><a href="tel:{{ str_replace(' ', '', $setting->phone) }}">{{ $setting->phone }}</a>
                                    </h5>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--====== End Pricing Section ======-->
    <!--====== Start FAQ Section ======-->
    <section class="faq-section pt-130 pb-80 bg-color-four">
        @include('frontend.components.faq');
    </section>
    <!--====== End FAQ Section ======-->


@stop
