@extends('frontend.layouts.app')
@section('title', 'Faqs' . ' ' . '-' . '' . $setting->site_title)
@section('keywords', $setting->site_keywords)
@section('description', $setting->site_description)
@section('content')

    <!-- Header Start -->
    <section class="page-title"
        style="background-image:linear-gradient(rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.75)), url({{ asset('public/assets/images/background/bread.webp') }}); background-size: cover; background-position: center;">
        <div class="auto-container">
            <h1>FAQ</h1>
            <ul class="bread-crumb clearfix">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li>FAQ</li>
            </ul>
        </div>
    </section>
    <!-- Header End -->



    <!-- FAQ Start -->
    @include('frontend.components.faq')
    <!-- FAQ End -->
@stop

@section('css')

@stop
