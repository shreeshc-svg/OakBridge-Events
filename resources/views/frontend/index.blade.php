@extends('frontend.layouts.app')
@section('title', $setting->bname . ' ' . '-' . '' . $setting->site_title)
@section('keywords', $setting->site_keywords)
@section('description', $setting->site_description)
@section('content')



    <!-- Carousel Start -->
    <div class="header-carousel owl-carousel">
        <div class="header-carousel-item">
            <div class="header-carousel-item-img-1">
                <img src="{{ asset('public/assets/img/banner-1-min.webp') }}" class="img-fluid w-100" alt="Image">
            </div>
            <div class="carousel-caption">
                <div class="carousel-caption-inner text-start p-3">
                    <h1 class="display-1 text-capitalize text-white mb-4 fadeInUp animate__animated" data-animation="fadeInUp"
                        data-delay="1.3s" style="animation-delay: 1.3s;">Connecting Top Talent with Leading Organizations
                    </h1>
                    <p class="mb-5 fs-5 fadeInUp animate__animated" data-animation="fadeInUp" data-delay="1.5s"
                        style="animation-delay: 1.5s;">Emphasizes the company’s expertise in matching skilled candidates
                        with top employers.
                    </p>
                    <a class="btn btn-primary rounded-pill py-3 px-5 mb-4 me-4 fadeInUp animate__animated"
                        data-animation="fadeInUp" data-delay="1.5s" style="animation-delay: 1.7s;"
                        href="{{ route('service') }}">View Jobs</a>

                </div>
            </div>
        </div>
        <div class="header-carousel-item mx-auto">
            <div class="header-carousel-item-img-2">
                <img src="{{ asset('public/assets/img/banner-2-min.webp') }}" class="img-fluid w-100" alt="Image">
            </div>
            <div class="carousel-caption">
                <div class="carousel-caption-inner text-start p-3">
                    <h1 class="display-1 text-capitalize text-white mb-4 fadeInUp animate__animated"
                        data-animation="fadeInUp" data-delay="1.3s" style="animation-delay: 1.3s;">Your Trusted Partner for
                        Optimized Recruitment</h1>
                    <p class="mb-5 fs-5 fadeInUp animate__animated" data-animation="fadeInUp" data-delay="1.5s"
                        style="animation-delay: 1.5s;">Highlights the company’s role in streamlining the hiring process with
                        reliability.
                    </p>
                    <a class="btn btn-primary rounded-pill py-3 px-5 mb-4 me-4 fadeInUp animate__animated"
                        data-animation="fadeInUp" data-delay="1.5s" style="animation-delay: 1.7s;"
                        href="{{ route('service') }}">View Jobs</a>

                </div>
            </div>
        </div>
        <div class="header-carousel-item">
            <div class="header-carousel-item-img-3">
                <img src="{{ asset('public/assets/img/banner-3-min.webp') }}" class="img-fluid w-100" alt="Image">
            </div>
            <div class="carousel-caption">
                <div class="carousel-caption-inner text-start p-3">
                    <h1 class="display-1 text-capitalize text-white mb-4 fadeInUp animate__animated"
                        data-animation="fadeInUp" data-delay="1.3s" style="animation-delay: 1.3s;">Delivering Excellence
                        with Transparency and Efficiency</h1>
                    <p class="mb-5 fs-5 fadeInUp animate__animated" data-animation="fadeInUp" data-delay="1.5s"
                        style="animation-delay: 1.5s;">Focuses on the company’s commitment to high-quality, transparent, and
                        efficient recruitment.
                    </p>
                    <a class="btn btn-primary rounded-pill py-3 px-5 mb-4 me-4 fadeInUp animate__animated"
                        data-animation="fadeInUp" data-delay="1.5s" style="animation-delay: 1.7s;"
                        href="{{ route('service') }}">View Jobs</a>

                </div>
            </div>
        </div>
    </div>
    <!-- Carousel End -->


    <!-- About Start -->
    @include('frontend.components.about')
    <!-- About End -->

    <!-- service Start -->
    <div class="container-fluid blog pb-5 mt-5">
        <div class="container pb-5">
            <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 800px;">
                <h4 class="text-primary">Our Jobs</h4>
                <h1 class="display-4">Latest Jobs</h1>
            </div>
            <div class="row g-4 justify-content-center">
                @foreach ($services as $service)
                    <div class="col-md-6 col-lg-6 col-xl-4 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="blog-item bg-light rounded p-4"
                            style="background-image: url({{ asset('uploads/images/services' . $service->image) }});">
                            <div class="project-img">
                                <img src="{{ asset('public/uploads/images/service/' . $service->image) }}"
                                    class="img-fluid w-100 rounded" alt="Image">

                                {{-- <div class="blog-plus-icon">
                                    <a href="{{ asset('public/assets/img/blog-1.jpg') }}" data-lightbox="blog-1"
                                        class="btn btn-primary btn-md-square rounded-pill"><i
                                            class="fas fa-plus fa-1x"></i></a>
                                </div> --}}
                            </div>
                            <div class="my-4">
                                <a href="#" class="h4">{{ $service->title }}</a>
                                <p>{{ Str::limit($service->excerpt, 140) }}</p>
                            </div>
                            <a class="btn btn-primary rounded-pill py-2 px-4"
                                href="{{ route('service.detail', $service->slug) }}">Explore More</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- services End -->

    @include('frontend.components.industries')

    <!-- Team Start -->
    @include('frontend.components.team')
    <!-- Team End -->


    <!-- Testimonial Start -->
    @include('frontend.components.testimonials')
    <!-- Testimonial End -->


    <!-- FAQ Start -->
    @include('frontend.components.faq')
    <!-- FAQ End -->

    <!-- Contact Start -->
    <div class="container-fluid contact bg-light py-5">
        <div class="container py-5">
            <div class="row g-5">
                @include('frontend.components.contact')

            </div>
        </div>
    </div>
    <!-- Contact End -->





@endsection
