@extends('frontend.layouts.app')
@section('title', 'Contact Us | India Law, AI & Tech Summit 2025')
@section('keywords', 'legal tech, law ai, tech summit, legal tech summit, new technology, technology summit, legal tech leaders, india law ai, ai in law, ai legal tech, ilats contact')
@section('description', '')
@section('content')


    <section class="page-title"
        style="background-image: url({{ asset('public/assets/images/background/bread2.webp') }}); background-size: cover; background-position: center;">
        <div class="auto-container">
            <h1>Contact Us</h1>
            <ul class="bread-crumb clearfix">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li>Contact Us</li>
            </ul>
        </div>
    </section>
    <!--End Page Title-->

    <!-- Contact Page Section -->
    <section class="contact-page-section">
        <div class="auto-container">
            <div class="row clearfix">
                <div class="contact-column col-lg-4 col-md-12 col-sm-12 order-2">
                    <div class="inner-column">
                        <div class="sec-title">
                            <h2>Event Info</h2>
                        </div>
                       <ul class="contact-info">
    @if ($setting->address)
        <li>
            <span class="icon fa fa-map-marker-alt"></span>
            <p>{{ $setting->address }}</p>
        </li>
    @endif

    @if ($setting->phone)
        <li>
            <span class="icon fa fa-phone-volume"></span>
            <p><strong>Call Us</strong></p>
            <p>{{ $setting->phone }}</p>
        </li>
    @endif
    @if ($setting->phone2)
        <li>
            <span class="icon fa fa-phone-volume"></span>
            <p><strong>Call Us</strong></p>
            <p>{{ $setting->phone2 }}</p>
        </li>
    @endif

    <li>
        <span class="icon fa fa-envelope"></span>
        <p><strong>Mail Us</strong></p>
        <p><a href="mailto:{{ $setting->email }}">{{ $setting->email }}</a></p>
        {{-- The extra '<' has been removed from the line above. --}}
    </li>
</ul>

                        <ul class="social-icon-two social-icon-colored">
                            @if ($setting->facebook)
                                <li><a target="_blank" href="{{ $setting->facebook }}"><i class="fab fa-facebook"></i></a>
                                </li>
                            @endif
                            @if ($setting->instagram)
                                <li><a target="_blank" href="{{ $setting->instagram }}"><i class="fab fa-instagram"></i></a>
                                </li>
                            @endif
                            @if ($setting->twitter)
                                <li><a target="_blank" href="{{ $setting->twitter }}"><i class="fab fa-twitter"></i></a>
                                </li>
                            @endif
                            @if ($setting->linkedin)
                                <li><a target="_blank" href="{{ $setting->linkedin }}"><i class="fab fa-linkedin"></i></a>
                                </li>
                            @endif
                            @if ($setting->youtube)
                                <li><a target="_blank" href="{{ $setting->youtube }}"><i class="fab fa-youtube"></i></a>
                                </li>
                            @endif

                        </ul>
                    </div>
                </div>

                <!-- Form Column -->
                <div class="form-column col-lg-8 col-md-12 col-sm-12">
                    <div class="inner-column">
                        <div class="contact-form">
                            <div class="sec-title">
                                <h2>Get in Touch</h2>
                            </div>
                            <form method="post" action="{{ route('contact.send') }}" id="contact-form">
                                @csrf
                                <div class="row clearfix">
                                    <div class="col-lg-6 col-md-6 col-sm-12 form-group @error('name') is-invalid @enderror">
                                        <input type="text" name="name" placeholder="Name" value="{{ old('name') }}"
                                            required>
                                        @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-sm-12 form-group">
                                        <input type="email" name="email" placeholder="Email"
                                            value="{{ old('email') }}" required>
                                        @error('email')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                                        <input type="text" name="phone" placeholder="Phone no."
                                            value="{{ old('phone') }}" required>
                                        @error('phone')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                                        <textarea name="message" placeholder="Message" value="{{ old('message') }}">{{ old('message') }}</textarea>
                                        @error('message')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                                        <button class="theme-btn btn-style-one" type="submit" name="submit-form"><span
                                                class="btn-title">Submit Now</span></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
 
    <section class="map-section">
        <div class="auto-container">
            <div class="map-outer">
                {!! $setting->map !!}
            </div>
        </div>
    </section>
    <!-- End Map Section -->
    
  @stop




