@extends('frontend.layouts.app')
@if ($search)
    @section('title', ucwords($search) . ' ' . '-' . ' ' . $setting->bname)
@elseif ($category)
    @section('title', ucwords($category) . ' ' . '-' . ' ' . $setting->site_title)
@else
    @section('title', 'Products' . ' ' . '-' . ' ' . $setting->site_title)
@endif
@section('description', $setting->site_description)
@section('keywords', $setting->site_keywords)
@section('content')

    <!-- Header Start -->
    <div class="container-fluid bg-breadcrumb">
        <div class="bg-breadcrumb-single"></div>
        <div class="container text-center py-5" style="max-width: 900px;">
            <h4 class="text-white display-4 mb-4 wow fadeInDown" data-wow-delay="0.1s">Our Job</h4>
            <ol class="breadcrumb justify-content-center mb-0 wow fadeInDown" data-wow-delay="0.3s">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active text-primary">Job</li>
            </ol>
        </div>
    </div>
    <!-- Header End -->


    <div class="container-fluid blog pb-5 mt-5">
        <div class="container pb-5">
            <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 800px;">
                <h4 class="text-primary">Our Job</h4>
                <h1 class="display-4">Latest Job</h1>
            </div>
            <div class="row g-4 justify-content-center">
                <div class="col-md-8">
                    <div class="row">
                        @foreach ($services as $service)
                            <div class="col-md-6 col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                                <div class="blog-item bg-light rounded p-4"
                                    style="background-image: url({{ asset('uploads/images/services' . $service->image) }});">
                                    <div class="project-img">
                                        <img src="{{ asset('public/uploads/images/service/' . $service->image) }}"
                                            class="img-fluid w-100 rounded" alt="Image">

                                        {{-- <div class="blog-plus-icon">
                                    <a href="{{ asset('public/uploads/images/service/' . $service->image) }}"
                                        data-lightbox="blog-1" class="btn btn-primary btn-md-square rounded-pill"><i
                                            class="fas fa-plus fa-1x"></i></a>
                                </div> --}}
                                    </div>
                                    <div class="my-4">
                                        <a href="{{ route('service.detail', $service->slug) }}"
                                            class="h4">{{ $service->title }}</a>
                                        <p>{{ Str::limit($service->excerpt, 140) }}</p>
                                    </div>
                                    <a class="btn btn-primary rounded-pill py-2 px-4"
                                        href="{{ route('service.detail', $service->slug) }}">Explore More</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-4 col-md-8 col-10 col-xxs-12 px-md-5">
                    <div class="rv-blog-details-right rv-blog-details-search">
                        <h3 class="rv-blog-details-right__title">Search</h3>
                        <form action="{{ route('service') }}" method="get">
                            <div class="input-group">
                                <input type="text" class="form-control p-3" placeholder="Keyword" name="search">
                                <button type="submit" class="btn btn-primary "><i class="fa fa-search"
                                        aria-hidden="true"></i></button>
                            </div>
                        </form>
                    </div>

                    <div class="p-3 card mt-4">
                        <h3 class="mb-0">Categories</h3>
                        <hr>
                        @foreach ($service_categories as $category)
                            <p class=" h5">
                                <a href="{{ route('service', $category->slug) }}"class="text-secondary">
                                    <span class="">{{ $category->title }}
                                        ({{ $category->services->count() }})
                                    </span>
                                </a>
                            </p>
                        @endforeach

                    </div>

                    {{-- <div class="rv-blog-details-right rv-blog-details-recents wow fadeInRight mt-4 card p-3 mb-5">
                        <h3 class="rv-blog-details-right__title mb-3">Request Callback</h3>
                        <div class="rv-recent-blog ">
                            <form action="{{ route('contact.send') }}" method="POST">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-lg-12 ">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" name="name"
                                                placeholder="Your Name">
                                            @error('name')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                            <label for="name">Your Name</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 ">
                                        <div class="form-floating">
                                            <input type="email" class="form-control" name="email"
                                                placeholder="Your Email">
                                            @error('email')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                            <label for="email">Your Email</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-xl-12">
                                        <div class="form-floating">
                                            <input type="phone" class="form-control" name="phone" placeholder="Phone">
                                            @error('phone')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                            <label for="phone">Your Phone</label>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-floating">
                                            <textarea class="form-control" placeholder="Leave a message here" name="message" style="height: 160px"></textarea>
                                            @error('message')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                            <label for="message">Message</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-primary w-100 py-3">Send Message</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div> --}}
                </div>
            </div>
        </div>
    </div>


    <!-- Service End -->

@stop

@section('css')

    <style>
        .sticky-sidebar {
            position: sticky;
            top: 0;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            height: 100vh;
        }
    </style>

@stop
