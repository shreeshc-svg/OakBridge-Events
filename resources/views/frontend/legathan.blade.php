@extends('frontend.layouts.app')
@section('title', 'Legathan' . ' ' . '-' . ' ' . $setting->site_title)
@section('keywords', $setting->site_keywords)
@section('description', $setting->site_description)
@section('content')



    <!--Page Title-->
    <section class="page-title"
        style="background-image:linear-gradient(rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.75)), url({{ asset('public/assets/images/background/bread.webp') }}); background-size: cover; background-position: center;">
        {{-- <section class="page-title" style="background-image:url({{ asset('public/assets/images/background/5.jpg') }});"> --}}
        <div class="auto-container">
            <h1>{{ \App\Support\PageContent::get('legathon')['page_title'] }}</h1>
            <ul class="bread-crumb clearfix">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li>{{ \App\Support\PageContent::get('legathon')['page_title'] }}</li>
            </ul>
        </div>
    </section>
    <!--End Page Title-->


    {{-- Competitions: Admin > Legathon --}}
    @php
        try {
            $competitions = \App\Models\Competition::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
        } catch (\Throwable $e) {
            $competitions = collect();
        }
    @endphp
    @foreach ($competitions as $competition)
        <section class="py-5">
            <div class="container">
                <div class="row g-4 align-items-center">
                    <div class="{{ $competition->image ? 'col-md-7' : 'col-md-12' }} order-2 order-md-1">
                        <div class="sec-title">
                            <h2>{{ $competition->title }}</h2>
                            @if ($competition->subtitle)
                                <p class="title mb-0 mt-2">{{ $competition->subtitle }}</p>
                            @endif
                            <div class="text mb-2 page-rich-text rich-ticks">{!! $competition->description !!}</div>
                            @if (count($competition->buttons ?? []))
                                <div class="d-md-flex mt-4">
                                    @foreach ($competition->buttons as $button)
                                        <div class="btn-box mr-3 mb-2">
                                            <a href="{{ \App\Support\SiteMenu::href($button['url']) }}"
                                                @if (!empty($button['new_tab'])) target="_blank" rel="noopener" @endif
                                                class="theme-btn btn-style-{{ in_array($button['style'] ?? '', ['one', 'two', 'three']) ? $button['style'] : 'one' }} px-3 py-1"><span
                                                    class="btn-title">{{ $button['label'] }}</span></a>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    @if ($competition->image)
                        <div class="col-md-5 order-1 order-md-2">
                            <img class="img-fluid" src="{{ \App\Support\Uploads::url($competition->image) }}" alt="{{ $competition->title }}">
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endforeach

@stop

@section('js')

@stop
