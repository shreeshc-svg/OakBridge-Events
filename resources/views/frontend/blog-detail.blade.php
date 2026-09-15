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
    <!--Page Title-->

    {{-- <section class="page-title" style="background-image:url({{ asset('public/assets/images/background/5.jpg') }});"> --}}
    <section class="page-title"
        style="background-image:linear-gradient(rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.75)), url({{ asset('public/assets/images/background/bread.webp') }}); background-size: cover; background-position: center;">
        <div class="auto-container">

            <h1>{{ $post->title }}</h1>

            <ul class="bread-crumb clearfix">

                <li><a href="{{ route('home') }}">Home</a></li>

                <li>{{ $post->title }}</li>

            </ul>

        </div>

    </section>

    <!--End Page Title-->



    <!--Sidebar Page Container-->

    <div class="sidebar-page-container">

        <div class="auto-container">

            <div class="row clearfix">



                <!--Content Side / Blog Sidebar-->

                <div class="content-side col-lg-8 col-md-12 col-sm-12">

                    <div class="blog-single">

                        <!-- News Block Three -->

                        <div class="news-block">

                            <div class="inner-box">

                                @if ($post->image)
                                    <div class="image-box">

                                        <figure class="image rounded"><img
                                                src="{{ asset('public/uploads/images/post/' . $post->image) }}"
                                                alt="">
                                        </figure>

                                    </div>
                                @endif

                                <div class="lower-content">

                                    <ul class="post-info">

                                        <li><span class="far fa-user"></span> Admin</li>

                                        <li class="float-right"><span class="far fa-folder"></span>
                                            @foreach ($post->categories as $category)
                                                {{ $category->title }}@if (!$loop->last)
                                                    ,
                                                @endif
                                            @endforeach
                                        </li>

                                    </ul>

                                    <h2>{{ $post->title }}</h2>
                                    {!! $post->body !!}

                                </div>

                            </div>

                        </div>
                        <hr>

                    </div>

                </div>



                <!--Sidebar Side-->

                <div class="sidebar-side col-lg-4 col-md-12 col-sm-12">

                    <aside class="sidebar padding-left">



                        <!-- Search -->

                        <div class="sidebar-widget search-box">

                            <form method="get" action="{{ route('blog') }}">

                                <div class="form-group">

                                    <input type="search" name="search" value="" placeholder="Search..." required>

                                    <button type="submit"><span class="icon fa fa-search"></span></button>

                                </div>

                            </form>

                        </div>



                        <!-- Category Widget -->

                        <div class="sidebar-widget categories">

                            <h4 class="sidebar-title">Categories</h4>

                            <div class="widget-content">

                                <!-- Blog Category -->

                                <ul class="blog-categories">

                                    @foreach ($categories as $category)
                                        <li><a href="{{ route('blog', ['category' => $category->slug]) }}">{{ $category->title }}
                                                <span>{{ $category->posts->count() }}</span></a></li>
                                    @endforeach

                                </ul>

                            </div>

                        </div>



                        <!-- Post Widget -->

                        <div class="sidebar-widget popular-posts">

                            <h4 class="sidebar-title">Latest Posts</h4>

                            <div class="widget-content">



                                @foreach ($recently as $post)
                                    <article class="post">

                                        <div class="post-inner">

                                            <figure class="post-thumb"><a
                                                    href="{{ route('blog.detail', $post->slug) }}"><img
                                                        src="{{ asset('public/uploads/images/post/' . $post->image) }}"
                                                        alt=""></a></figure>

                                            <div class="post-info">{{ $post->created_at->format('d M Y') }}</div>

                                            <div class="text"><a
                                                    href="{{ route('blog.detail', $post->slug) }}">{{ Str::limit($post->title, 40) }}</a>
                                            </div>

                                        </div>

                                    </article>
                                @endforeach


                            </div>

                        </div>



                        <!-- Tags Widget -->

                        @if ($post->tags)
                            <div class="sidebar-widget popular-tags">

                                <h4 class="sidebar-title">Tags</h4>

                                <div class="widget-content">

                                    @foreach ($post->tags as $tag)
                                        <a href="#">#{{ $tag->title }}</a>
                                    @endforeach

                                </div>

                            </div>
                        @endif

                    </aside>

                </div>

            </div>

        </div>

    </div>

    <!-- End Sidebar Page Container -->




@stop
