@extends('frontend.layouts.app')
@section('title', 'Testimonials | What People Say About India Law, AI & Summit 2025')
@section('keywords', 'legal tech, law ai, tech summit, legal tech summit, new technology, technology summit, legal tech leaders, india law ai, ai in law, ai legal tech')
@section('description', 'Read testimonials from attendees, speakers, and participants of ILATS 2025. Discover their experiences and insights about India’s premier Legal Tech & AI Festival.')
@section('content')

    <!--Page Title-->
    {{-- <section class="page-title" style="background-image:url({{ asset('public/assets/images/background/5.jpg') }});"> --}}
    <section class="page-title"
        style="background-image:linear-gradient(rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.75)), url({{ asset('public/assets/images/background/bread2.webp') }}); background-size: cover; background-position: center;">
        <div class="auto-container">
            <h1>Testimonials</h1>
            <ul class="bread-crumb clearfix">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li>Testimonials</li>
            </ul>
        </div>
    </section>
    <!--End Page Title-->

    <section id="testimonials" class="py-5 bg-light">
        <div class="container">
            <div class="row mb-5">
                <h2 class="mx-auto ">What people say about us?</h2>
            </div>
            <div class="row mt-3" id="testimonial-alignment">
                <!-- Testimonials -->
                @foreach ($testimonials as $testimonial)
                    <div class="col-md-4 mb-4">
                        <div class="card p-3 h-100">
                            <div class="row testimonial-alignment">
                                <div class="col-md-3">

                                    <img class="testimonial-image rounded-circle"
                                        src="{{ asset('public/uploads/images/testimonial/' . $testimonial->image) }}"
                                        alt="">


                                </div>
                                <div class="col-md-9">
                                    <h5 class="mb-0 pb-0">{{ $testimonial->title }}</h5>
                                    <p class="mb-0">{{ $testimonial->location }}</p>
                                    <span>{{ $testimonial->star }}</span>

                                </div>
                            </div>
                            <div class="row p-3 testimonial-alignment">
                                <p>
                                    {{ $testimonial->body }}
                                </p>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>




@stop

@section('css')

@stop

@section('js')

@stop
