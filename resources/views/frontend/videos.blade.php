@extends('frontend.layouts.app')
@section('title', 'Videos' . ($edition ? ' ' . $edition->year : '') . ' - ' . $setting->site_title)
@section('keywords', $setting->site_keywords)
@section('description', $setting->site_description)
@section('content')



    <!--Page Title-->
    <section class="page-title"
        style="background-image: url({{ asset('public/assets/images/background/bread2.webp') }}); background-size: cover; background-position: center;">
        
      {{--  <section class="page-title"
        style="background-image:linear-gradient(rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.75)), url({{ asset('public/assets/images/background/bread.webp') }}); background-size: cover; background-position: center;"> --}}
        {{-- <section class="page-title" style="background-image:url({{ asset('public/assets/images/background/5.jpg') }});"> --}}
        <div class="auto-container">
            <h1>Videos{{ $edition ? ' ' . $edition->year : '' }}</h1>
            <ul class="bread-crumb clearfix">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li>Gallery</li>
                @if ($edition)<li>{{ $edition->year }}</li>@endif
                <li>Videos</li>
            </ul>
        </div>
    </section>
    <!--End Page Title-->
    @include('frontend.components.year-tabs', ['section' => 'videos'])
    <section class="py-5">
        <div class="container">
            <div class="row">

                @foreach ($videos as $video)
                    @php $embedUrl = $video->embedUrl(); @endphp
                    @continue(! $embedUrl)

                    <div class="col-md-4 mb-5">
                        <div class="card p-2">
                            <iframe width="100%" height="220" src="{{ $embedUrl }}" title="{{ $video->title }}" loading="lazy" frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen>
                            </iframe>
                            <h4 class="text-center p-1">{{ $video->title }}</h4>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>



@stop

@section('js')

@stop
