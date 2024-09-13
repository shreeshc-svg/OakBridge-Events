@extends('frontend.layouts.app')
@section('title', 'Return Policy' . ' ' . '-' . '' . $setting->site_title)
@section('keywords', $setting->site_keywords)
@section('description', $setting->site_description)
@section('content')

    <!--====== Start Breadcrumb Section ======-->
    <section class="page-banner bg_cover p-r z-1 text-white"
        style="background-image: url(public/assets/images/bg/breadcrumbs-bg.jpg);">
        <div class="container">
            <h1 class="page-title">Return Policy</h1>
            <div class="page-breadcrumb">
                <ul class="breadcrumb-link">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li class="active">Return Policy</li>
                </ul>
            </div>
        </div>
    </section>
    <!--====== End Breadcrumb Section ======-->

    <section class="about-section-shape pt-100 pb-70 p-r z-1">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="about-two_content-box content-box-gap wow fadeInRight mb-50"
                        style="visibility: visible; animation-name: fadeInRight;">
                        <div class="section-title mb-30 text-center">
                            <span class="sub-title"><span class="line line1"></span>Policy</span>
                            <h2>Return Policy</h2>
                        </div>
                        <div class="text-justify mb-30">
                            <p><strong>Return Policy</strong></p>
                            <p>Your content here..</p>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@stop
