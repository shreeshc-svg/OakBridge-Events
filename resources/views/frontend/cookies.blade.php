@extends('frontend.layouts.app')
@section('title', 'Cookies' . ' ' . '-' . '' . $setting->site_title)
@section('keywords', $setting->site_keywords)
@section('description', $setting->site_description)
@section('content')

    <!--====== Start Breadcrumb Section ======-->
    <section class="page-banner bg_cover p-r z-1 text-white"
        style="background-image: url(public/assets/images/bg/breadcrumbs-bg.jpg);">
        <div class="container">
            <h1 class="page-title">Cookies</h1>
            <div class="page-breadcrumb">
                <ul class="breadcrumb-link">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li class="active">Cookies</li>
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
                            <h2>Cookie Policy</h2>
                        </div>
                        <div class="text-justify mb-30">
                            <p>To enrich and perfect your online experience, Garage Vaala uses "Cookies", similar
                                technologies and services provided by others to display personalized content, appropriate
                                advertising and store your preferences on your computer.</p>
                            <p>&nbsp;</p>

                            <p>A cookie is a string of information that a website stores on a visitor's computer, and that
                                the visitor's browser provides to the website each time the visitor returns. Garage Vaala
                                uses cookies to help Garage Vaala identify and track visitors, their usage of
                                https://www.garagevaala.com, and their website access preferences. Garage Vaala visitors who
                                do not wish to have cookies placed on their computers should set their browsers to refuse
                                cookies before using Garage Vaala's websites, with the drawback that certain features of
                                Garage Vaala's websites may not function properly without the aid of cookies.</p>
                            <p>&nbsp;</p>

                            <p>By continuing to navigate our website without changing your cookie settings, you hereby
                                acknowledge and agree to Garage Vaala's use of cookies.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
