@extends('frontend.layouts.app')
@section('title', 'Distinguished Speakers | India Law, AI & Tech 2025 ')
@section('keywords', 'ILATS 2025 speakers, legal experts, keynote speakers, law conference panelists, legal tech summit, law and ai leaders')
@section('description', 'Discover the distinguished speakers at India Law, AI & Tech, including top legal minds, technologists, and industry experts. Explore their insights and contributions to the legal tech summit.')
@section('content')
    <!--Page Title-->

    {{-- <section class="page-title" style="background-image:url({{ asset('public/assets/images/background/5.jpg') }});"> --}}
    <section class="page-title"
        style="background-image: url({{ asset('public/assets/images/background/bread2.webp') }}); background-size: cover; background-position: center;">

        <div class="auto-container">

            <h1>Speakers</h1>

            <ul class="bread-crumb clearfix">

                <li><a href="{{ route('home') }}">Home</a></li>

                <li>Speakers</li>

            </ul>

        </div>

    </section>

    <!--End Page Title-->

 @include('frontend.components.speakers')


@stop
