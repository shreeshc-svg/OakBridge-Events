@extends('frontend.layouts.app')
@if ($post->meta_title)
    @section('title', $post->meta_title . ' ' . '-' . ' ' . $setting->site_title)
@else
    @section('title', $setting->bname . ' ' . '-' . ' ' . $post->title)
@endif
@if ($post->meta_description)
    @section('description', $post->meta_description)
@else
    @section('description', Str::limit($post->excerpt, 200))
@endif
@section('keywords', $post->meta_keyword)
@section('content')
    <!-- Header Start -->
    <div class="container-fluid bg-breadcrumb">
        <div class="bg-breadcrumb-single"></div>
        <div class="container text-center py-5" style="max-width: 900px;">
            <h4 class="text-white display-4 mb-4 wow fadeInDown" data-wow-delay="0.1s">{{ $post->title }}</h4>
            <ol class="breadcrumb justify-content-center mb-0 wow fadeInDown" data-wow-delay="0.3s">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active text-primary">{{ $post->title }}</li>
            </ol>
        </div>
    </div>
    <!-- Header End -->

    <!-- BLOG DETAILS START -->
    <div class="rv-blog-details pt-120 pb-120 py-5">
        <div class="container">
            <div class="row justify-content-center ">
                <div class="col-lg-8">
                    <div class="rv-blog-details-left">
                        @if ($post->image)
                            <div class="rv-blog-details__img">
                                <img class="w-100" src="{{ asset('public/uploads/images/post/' . $post->image) }}"
                                    alt="blog banner">
                            </div>
                        @endif

                        <div class="d-flex py-3">
                            <span class=""><i class="fa fa-calendar" aria-hidden="true"></i>
                                <span>{{ date('d, M Y') }}</span>
                            </span>
                            <span class="mx-3"><i class="fa fa-folder" aria-hidden="true"></i>
                                <span>
                                    @foreach ($post->categories as $category)
                                        {{ $post->title }}
                                    @endforeach
                                </span>
                            </span>

                        </div>

                        <h2 class="rv-blog-details__title">{{ $post->title }}</h2>

                        <div class="mb-5">
                            {!! $post->body !!}
                        </div>

<hr>

                    </div>
                </div>

                <div class="col-lg-4 col-md-8 col-10 col-xxs-12">
                    <div class="rv-blog-details-right rv-blog-details-search">
                        <h3 class="rv-blog-details-right__title">Search</h3>
                        <form action="{{ route('blog') }}" method="get">
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
                        @foreach ($categories as $category)
                            <p class=" h5">
                                <a href="{{ route('blog', $category->slug) }}"class="text-secondary">
                                    <span class="">{{ $category->title }}

                                    </span>
                                </a>
                            </p>
                        @endforeach

                    </div>

                    {{-- <div class="rv-blog-details-right rv-blog-details-recents wow fadeInRight mt-4 ">
                        <h3 class="rv-blog-details-right__title">Request Callback</h3>
                        <div class="rv-recent-blog">
                            <form action="{{ route('contact.send') }}" method="POST">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-lg-12 col-xl-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" name="name"
                                                placeholder="Your Name">
                                            @error('name')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                            <label for="name">Your Name</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-xl-6">
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
    <!-- BLOG DETAILS END -->


@stop
