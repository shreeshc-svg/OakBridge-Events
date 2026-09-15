@extends('frontend.layouts.app')
@section('title', 'Privacy Policy | India Law, AI & Tech Summit 2025')
@section('keywords', 'legal tech, law ai, tech summit, legal tech summit, new technology, technology summit, legal tech leaders, india law ai, ai in law, ai legal tech')
@section('description', 'Read the Privacy Policy of India Law, AI & Tech Summit 2025 to understand how we collect, use, and protect your data. Your privacy and security are our top priorities.')
@section('content')

    <!--====== Start Breadcrumb Section ======-->

    {{-- <section class="page-title" style="background-image:url({{ asset('public/assets/images/background/5.jpg') }});"> --}}
    <section class="page-title"
        style="background-image: url({{ asset('public/assets/images/background/bread2.webp') }}); background-size: cover; background-position: center;">

        <div class="auto-container">

            <h1>{{ \App\Support\PageContent::get('privacy')['page_title'] }}</h1>

            <ul class="bread-crumb clearfix">

                <li><a href="{{ route('home') }}">Home</a></li>

                <li>{{ \App\Support\PageContent::get('privacy')['page_title'] }}</li>

            </ul>

        </div>

    </section>
    <!--====== End Breadcrumb Section ======-->

    @php $privacy = \App\Support\PageContent::get('privacy'); @endphp
    <section class="container py-5">
        <div class="row">
            <div class="col-12 page-rich-text">
                @if ($privacy['heading'])
                    <h1 class="text-center mb-4">{{ $privacy['heading'] }}</h1>
                @endif
                {!! $privacy['body'] !!}
            </div>
        </div>
    </section>
@stop
