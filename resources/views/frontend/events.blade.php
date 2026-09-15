@extends('frontend.layouts.app')
@section('title', 'Upcoming Events | India Law, AI & Tech Summit 2025')
@section('keywords', 'legal tech, law ai, tech summit, legal tech summit, new technology, technology summit, legal tech leaders, india law ai, ai in law, ai legal tech, oakbridge events')
@section('description', 'The India Law AI Tech Summit 2025 envisions Indias premier annual forum for legal innovation. A dynamic experience designed for maximum engagement and unparalled access celebrating Law AI Tech pioneers leaders and innovators driving Law AI Tech revolution.')
@section('content')

    <!--Page Title-->
    {{-- <section class="page-title" style="background-image:url({{ asset('public/assets/images/background/5.jpg') }});"> --}}
    <section class="page-title"
        style="background-image: url({{ asset('public/assets/images/background/bread2.webp') }}); background-size: cover; background-position: center;">
        <div class="auto-container">
            <h1>Events</h1>
            <ul class="bread-crumb clearfix">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li>Events</li>
            </ul>
        </div>
    </section>
    <!--End Page Title-->




    @include('frontend.components.events')




@stop

@section('js')

@stop
