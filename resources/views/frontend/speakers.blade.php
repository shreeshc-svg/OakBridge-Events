@extends('frontend.layouts.app')
@section('title', 'Distinguished Speakers' . ($edition ? ' ' . $edition->year : '') . ' | India Law, AI & Tech')
@section('keywords', 'ILATS speakers, legal experts, keynote speakers, law conference panelists, legal tech summit, law and ai leaders')
@section('description', 'Discover the distinguished speakers at India Law, AI & Tech, including top legal minds, technologists, and industry experts. Explore their insights and contributions to the legal tech summit.')
@section('content')
    <!--Page Title-->

    {{-- <section class="page-title" style="background-image:url({{ asset('public/assets/images/background/5.jpg') }});"> --}}
    {{-- Each year can have its own banner (Admin > Years > Edit). It is finished artwork with its own
         text, so it is shown whole - never cropped, nothing on top - and the page title sits underneath. --}}
    @if ($edition && $edition->speakers_banner)
        @php $speakersBanner = \App\Support\Uploads::url($edition->speakers_banner); @endphp
        <section class="ob-art-banner" style="--art: url('{{ $speakersBanner }}');">
            <img src="{{ $speakersBanner }}" alt="Speakers {{ $edition->year }}{{ $edition->title ? ' – ' . $edition->title : '' }}">
        </section>
        <section class="ob-art-title">
            <div class="auto-container">
                <h1>Speakers {{ $edition->year }}</h1>
                <ul class="bread-crumb clearfix">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('speakers') }}">Speakers</a></li>
                    <li>{{ $edition->year }}</li>
                </ul>
            </div>
        </section>
    @else
        <section class="page-title"
            style="background-image: url({{ asset('public/assets/images/background/bread2.webp') }}); background-size: cover; background-position: center;">

            <div class="auto-container">

                <h1>Speakers{{ $edition ? ' ' . $edition->year : '' }}</h1>

                <ul class="bread-crumb clearfix">

                    <li><a href="{{ route('home') }}">Home</a></li>

                    @if ($edition)
                        <li><a href="{{ route('speakers') }}">Speakers</a></li>
                        <li>{{ $edition->year }}</li>
                    @else
                        <li>Speakers</li>
                    @endif

                </ul>

            </div>

        </section>
    @endif

    <!--End Page Title-->

    @include('frontend.components.year-tabs', ['section' => 'speakers'])

    @include('frontend.components.speakers')


@stop
