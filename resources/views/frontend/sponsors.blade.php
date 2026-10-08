@extends('frontend.layouts.app')
@section('title', 'Sponsors & Exhibitors' . ($edition ? ' ' . $edition->year : '') . ' - ' . $setting->site_title)
@section('keywords', $setting->site_keywords)
@section('description', 'Partners, sponsors and exhibitors' . ($edition ? ' of ' . $edition->year : '') . ' - ' . $setting->site_title)
@section('content')
    <section class="page-title"
        style="background-image: url({{ asset('public/assets/images/background/bread2.webp') }}); background-size: cover; background-position: center;">
        <div class="auto-container">
            <h1>Sponsors{{ $edition ? ' ' . $edition->year : '' }}</h1>
            <ul class="bread-crumb clearfix">
                <li><a href="{{ route('home') }}">Home</a></li>
                @if ($edition)
                    <li><a href="{{ route('sponsors') }}">Sponsors</a></li>
                    <li>{{ $edition->year }}</li>
                @else
                    <li>Sponsors</li>
                @endif
            </ul>
        </div>
    </section>

    @include('frontend.components.year-tabs', ['section' => 'sponsors'])

    @include('frontend.components.sponsors', ['sponsorEdition' => $edition])

    @unless ($edition)
        <section class="py-5"><div class="auto-container text-center text-muted">Sponsors will be announced soon.</div></section>
    @endunless
@stop
