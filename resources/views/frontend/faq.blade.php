@extends('frontend.layouts.app')
@section('title', 'Faqs' . ' ' . '-' . '' . $setting->site_title)
@section('keywords', $setting->site_keywords)
@section('description', $setting->site_description)
@section('content')

    <!-- Header Start -->
    <div class="container-fluid bg-breadcrumb">
        <div class="bg-breadcrumb-single"></div>
        <div class="container text-center py-5" style="max-width: 900px;">
            <h4 class="text-white display-4 mb-4 wow fadeInDown" data-wow-delay="0.1s">Our FAQs</h4>
            <ol class="breadcrumb justify-content-center mb-0 wow fadeInDown" data-wow-delay="0.3s">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active text-primary">FAQs</li>
            </ol>
        </div>
    </div>
    <!-- Header End -->



    <!-- FAQ Start -->
    @include('frontend.components.faq')
    <!-- FAQ End -->
@stop

@section('css')

@stop
