@extends('frontend.layouts.app')
@if ($search)
    @section('title', ucwords($search) . ' ' . '-' . ' ' . $setting->bname)
@elseif ($category)
    @section('title', ucwords($category) . ' ' . '-' . ' ' . $setting->site_title)
@elseif ($tag)
    @section('title', ucwords($tag) . ' ' . '-' . ' ' . $setting->site_title)
@else
    @section('title', 'Blogs' . ' ' . '-' . ' ' . $setting->site_title)
@endif
@section('description', $setting->site_description)
@section('keywords', $setting->site_keywords)
@section('content')
    <!-- Header Start -->
    <div class="container-fluid bg-breadcrumb">
        <div class="bg-breadcrumb-single"></div>
        <div class="container text-center py-5" style="max-width: 900px;">
            <h4 class="text-white display-4 mb-4 wow fadeInDown" data-wow-delay="0.1s">Our Blogs</h4>
            <ol class="breadcrumb justify-content-center mb-0 wow fadeInDown" data-wow-delay="0.3s">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active text-primary">Blog</li>
            </ol>
        </div>
    </div>
    <!-- Header End -->

    <!-- Blog Start -->
    <div class="container-fluid blog py-5">
        <div class="container py-5">
            <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 800px;">
                <h4 class="text-primary">Our Blogs</h4>
                <h1 class="display-4">Latest Articles & News from the Blogs</h1>
            </div>
            <div class="row g-4 justify-content-center">
                @foreach ($posts as $post)
                    <div class="col-md-6 col-lg-6 col-xl-4 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="blog-item bg-light rounded p-4"
                            style="background-image: url({{ asset('uploads/images/post' . $post->image) }});">
                            <div class="project-img">
                                <img src="{{ asset('public/uploads/images/post/' . $post->image) }}"
                                    class="img-fluid w-100 rounded" alt="Image">

                                {{-- <div class="blog-plus-icon">
                                    <a href="{{ asset('public/uploads/images/post/' . $post->image) }}"
                                        data-lightbox="blog-1" class="btn btn-primary btn-md-square rounded-pill"><i
                                            class="fas fa-plus fa-1x"></i></a>
                                </div> --}}
                            </div>
                            <div class="my-4">
                                <a href="{{ route('blog.detail', $post->slug) }}"
                                    class="h4">{{ Str::limit($post->title, 50) }}</a>
                                <p>{{ Str::limit($post->excerpt, 140) }}</p>
                            </div>
                            <a class="btn btn-primary rounded-pill py-2 px-4"
                                href="{{ route('blog.detail', $post->slug) }}">Explore More</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- Blog End -->





@stop
