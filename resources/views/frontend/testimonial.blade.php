@extends('frontend.layouts.app')
@section('title', 'Testimonials' . ' ' . '-' . '' . $setting->site_title)
@section('keywords', $setting->site_keywords)
@section('description', $setting->site_description)
@section('content')
    <!-- Header Start -->
    <!-- Navbar Start -->
    <div class="container-fluid position-relative p-0">
        @include('frontend.components.navbar')

        <div class="container-fluid bg-primary py-5 bg-header" style="margin-bottom: 90px;">
            <div class="row py-5">
                <div class="col-12 pt-lg-5 mt-lg-5 text-center">
                    <h1 class="display-4 text-white animated zoomIn">Testimonials</h1>
                    <a href="{{ route('home') }}" class="h5 text-white">Home</a>
                    <i class="far fa-circle text-white px-2"></i>
                    <a href="#" class="h5 text-white">Testimonials</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Testimonial Start -->
    <div class="container-fluid py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="section-title text-center position-relative pb-3 mb-4 mx-auto" style="max-width: 600px;">
                <h5 class="fw-bold text-primary text-uppercase">Testimonial</h5>
                <h1 class="mb-0">What Our Clients Say About Our Digital Services</h1>
            </div>
            <div class="owl-carousel testimonial-carousel wow fadeInUp" data-wow-delay="0.6s">
                @foreach ($testimonials as $testimonial)
                    <div class="testimonial-item bg-light my-4">
                        <div class="d-flex align-items-center border-bottom pt-5 pb-4 px-5">
                            <img class="img-fluid rounded"
                                src="{{ asset('public/uploads/images/testimonial/' . $testimonial->image) }}"
                                style="width: 60px; height: 60px;">
                            <div class="ps-4">
                                <h4 class="text-primary mb-1">{{ $testimonial->title }}</h4>
                                <small class="text-uppercase">{{ $testimonial->star }}</small><br>
                                <small class="text-uppercase">{{ $testimonial->location }}</small>
                            </div>
                        </div>
                        <div class="pt-4 pb-5 px-5">
                            {{ $testimonial->body }}
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
    <!-- Testimonial End -->


    <!-- Testimonial Start -->
    {{-- <div class="container-xxl py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container">
            <div class="text-center">
                <h6 class="section-title bg-white text-center text-primary px-3">Testimonial</h6>
                <h1 class="mb-5">What Patients say about us!</h1>
            </div>
            <div class="owl-carousel testimonial-carousel position-relative">
                @foreach ($testimonials as $testimonial)
                    <div class="testimonial-item text-center">
                        <img class="border rounded-circle p-2 mx-auto mb-3"
                            src="{{ asset('public/uploads/images/testimonial/' . $testimonial->image) }}"
                            style="width: 80px; height: 80px;">
                        <h5 class="mb-0">{{ $testimonial->title }}</h5>
                        <p class="mb-0">{{ $testimonial->location }}</p>
                        <span>{{ $testimonial->star }}</span>
                        <div class="testimonial-text bg-light text-center p-4 mt-3">
                            <p class="mb-0">{{ $testimonial->body }}</p>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div> --}}
    <!-- Testimonial End -->
@stop
