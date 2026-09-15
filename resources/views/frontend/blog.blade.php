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
    <!--Page Title-->

    {{-- <section class="page-title" style="background-image:url({{ asset('public/assets/images/background/5.jpg') }});"> --}}
    <section class="page-title"
        style="background-image:linear-gradient(rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.75)), url({{ asset('public/assets/images/background/bread.webp') }}); background-size: cover; background-position: center;">

        <div class="auto-container">

            <h1>Blogs</h1>

            <ul class="bread-crumb clearfix">

                <li><a href="{{ route('home') }}">Home</a></li>

                <li>Blogs</li>

            </ul>

        </div>

    </section>

    <!--End Page Title-->



    <!-- News Section -->

    <section class="news-section alternate">

        <div class="auto-container">

            <div class="row">



                <!-- News Block Three -->

                @foreach ($posts as $post)
                    <div class="news-block col-lg-4 col-md-6 col-sm-12 wow fadeInRight">

                        <div class="inner-box">

                            <div class="image-box">

                                <figure class="image"><a href="{{ route('blog.detail', $post->slug) }}"><img
                                            src="{{ asset('public/uploads/images/post/' . $post->image) }}"
                                            alt=""></a>
                                </figure>

                            </div>

                            <div class="lower-content">

                                <ul class="post-info">

                                    <li><span class="far fa-user"></span> Admin</li>

                                    <li><span class="far fa-folder"></span>
                                        @foreach ($post->categories as $category)
                                            {{ $category->title }}@if (!$loop->last)
                                                ,
                                            @endif
                                        @endforeach
                                    </li>

                                </ul>

                                <h4><a href="{{ route('blog.detail', $post->slug) }}">{{ $post->title }}</a></h4>

                                <div class="btn-box"><a href="{{ route('blog.detail', $post->slug) }}"
                                        class="read-more">Read More</a>
                                </div>

                            </div>

                        </div>

                    </div>
                @endforeach

            </div>

            {{ $posts->links() }}

        </div>

    </section>

    <!--End News Section -->





@stop
