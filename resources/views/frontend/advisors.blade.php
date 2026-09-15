@extends('frontend.layouts.app')
@section('title', 'Meet Our Partners and Sponsors | India Law, AI & Tech Summit 2025')
@section('keywords', 'advisors, legal experts, legal tech, law ai, tech summit, legal tech summit, new technology, technology summit, legal tech leaders, india law ai, ai in law, ai legal tech, legal advisors, consultation')
@section('description', 'Explore the diverse panel of expert advisors at ILATS, offering comprehensive legal insights and consultation. Connect with seasoned professionals dedicated to guiding you through legal complexities effectively.')
@section('content')

    <section class="page-title"
        style="background-image:linear-gradient(rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.75)), url({{ asset('public/assets/images/background/bread.webp') }}); background-size: cover; background-position: center;">

        {{-- <section class="page-title" style="background-image:url({{ asset('public/assets/images/background/5.jpg') }});"> --}}

        <div class="auto-container">

            <h1>Partners & Sponsors</h1>

            <ul class="bread-crumb clearfix">

                <li><a href="{{ route('home') }}">Home</a></li>

                <li>Sponsors</li>

            </ul>

        </div>

    </section>

    <!-- About Section -->

    @include('frontend.components.advisors')


@stop

@section('css')


@stop
