@extends('frontend.layouts.app')
@section('title', 'Thanks' . ' ' . '-' . ' ' . $setting->site_title)
@section('keywords', $setting->site_keywords)
@section('description', $setting->site_description)
@section('content')

    <!--Page Title-->
    {{-- <section class="page-title" style="background-image:url({{ asset('public/assets/images/background/5.jpg') }});"> --}}
    <section class="page-title"
        style="background-image:linear-gradient(rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.75)), url({{ asset('public/assets/images/background/bread.webp') }}); background-size: cover; background-position: center;">
        <div class="auto-container">
            <h1>Thanks</h1>
            <ul class="bread-crumb clearfix">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li>Thanks</li>
            </ul>
        </div>
    </section>
    <!--End Page Title-->

    <section class="py-5">
        <div class="container py-5">
            <div class="row justify-content-center text-center">
                <div class="col-md-8">
                    <h2>Thanks for being awesome!</h2>
                    <p>
                        We have received your message one of our team mate will be in touch with you as soon as possible.
                    </p>
                    <a href="{{ route('home') }}" class="mt-4 btn btn-primary">Back to home</a>
                </div>
            </div>
        </div>
    </section>

@stop

@section('js')

@stop
