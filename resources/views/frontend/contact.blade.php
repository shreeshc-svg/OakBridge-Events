@extends('frontend.layouts.app')
@section('title', 'Contact Us' . ' ' . '-' . '' . $setting->site_title)
@section('keywords', $setting->site_keywords)
@section('description', $setting->site_description)
@section('content')

    <!-- Header Start -->
    <div class="container-fluid bg-breadcrumb">
        <div class="bg-breadcrumb-single"></div>
        <div class="container text-center py-5" style="max-width: 900px;">
            <h4 class="text-white display-4 mb-4 wow fadeInDown" data-wow-delay="0.1s">Contact Us</h4>
            <ol class="breadcrumb justify-content-center mb-0 wow fadeInDown" data-wow-delay="0.3s">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active text-primary">Contact</li>
            </ol>
        </div>
    </div>
    <!-- Header End -->

    <!-- Contact Start -->
    <div class="container-fluid contact bg-light py-5">
        <div class="container py-5">
            <div class="row g-5 align-items-center">

                @include('frontend.components.contact')

                <div class="col-12 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="rounded h-100">
                        {!! $setting->map !!}
                        <div class="d-flex align-items-center justify-content-center bg-primary rounded-bottom p-4">
                            <div class="d-flex">
                                @if ($setting->facebook)
                                    <a target="_blank" class="btn btn-dark btn-lg-square rounded-circle me-2"
                                        href="{{ $setting->facebook }}"><i class="fab fa-facebook-f"></i></a>
                                @endif
                                @if ($setting->twitter)
                                    <a target="_blank" class="btn btn-dark btn-lg-square rounded-circle mx-2"
                                        href="{{ $setting->twitter }}"><i class="fab fa-twitter"></i></a>
                                @endif
                                @if ($setting->instagram)
                                    <a target="_blank" class="btn btn-dark btn-lg-square rounded-circle mx-2"
                                        href="{{ $setting->instagram }}"><i class="fab fa-instagram"></i></a>
                                @endif
                                @if ($setting->linkedin)
                                    <a target="_blank" class="btn btn-dark btn-lg-square rounded-circle mx-2"
                                        href="{{ $setting->linkedin }}"><i class="fab fa-linkedin-in"></i></a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Contact End -->

@stop

@section('js')

@stop
