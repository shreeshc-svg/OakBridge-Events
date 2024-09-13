@extends('frontend.layouts.app')
@section('title', 'Grievence' . ' ' . '-' . '' . $setting->site_title)
@section('keywords', $setting->site_keywords)
@section('description', $setting->site_description)
@section('content')

    <!-- Header Start -->
    <div class="container-fluid bg-breadcrumb">
        <div class="bg-breadcrumb-single"></div>
        <div class="container text-center py-5" style="max-width: 900px;">
            <h4 class="text-white display-4 mb-4 wow fadeInDown" data-wow-delay="0.1s">Grievence </h4>
            <ol class="breadcrumb justify-content-center mb-0 wow fadeInDown" data-wow-delay="0.3s">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active text-primary">Grievence </li>
            </ol>
        </div>
    </div>
    <!-- Header End -->

    <!-- Contact Start -->
    <div class="container-fluid contact bg-light py-5">
        <div class="container py-5">
            <div class="row mb-3 justify-content-center d-flex">
                <div class="col-md-8">
                    <h2>Grievance Form</h2>
                    <p>It is our endeavor to ensure that there is full transparency in our dealings with all our clients as
                        well as candidates. If you have any grievances please fill up the under mention form.</p>
                </div>
            </div>
            <div class="row g-5 justify-content-center">
                <div class="col-lg-8 wow fadeInRight" data-wow-delay="0.3s">
                    <form action="{{ route('grievence.form') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-lg-12 col-xl-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" value="{{ old('name') }}" name="name"
                                        placeholder="Your Name">
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <label for="name">Your Name</label>
                                </div>
                            </div>
                            <div class="col-lg-12 col-xl-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control" value="{{ old('email') }}" name="email"
                                        placeholder="Your Email">
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <label for="email">Your Email</label>
                                </div>
                            </div>
                            <div class="col-lg-12 col-xl-12">
                                <div class="form-floating">
                                    <input type="phone" class="form-control" value="{{ old('phone') }}" name="phone"
                                        placeholder="Phone">
                                    @error('phone')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <label for="phone">Your Phone</label>
                                </div>
                            </div>
                            <div class="col-lg-12 col-xl-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" value="{{ old('employer') }}" name="employer"
                                        placeholder="Employer Recruited ">
                                    @error('employer')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <label for="phone">Employer Recruited *</label>
                                </div>
                            </div>
                            <div class="col-lg-12 col-xl-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" value="{{ old('country') }}" name="country"
                                        placeholder="Country Recruited ">
                                    @error('country')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <label for="phone">Country Recruited *</label>
                                </div>
                            </div>
                            <div class="col-lg-12 col-xl-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" value="{{ old('city') }}" name="city"
                                        placeholder="city Recruited ">
                                    @error('city')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <label for="phone">City Recruited *</label>
                                </div>
                            </div>
                            <div class="col-lg-12 col-xl-12">
                                <div class="form-floating">
                                    <input type="date" class="form-control" value="{{ old('date') }}" name="date"
                                        placeholder="date Recruited ">
                                    @error('date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <label for="phone">Date Recruited *</label>
                                </div>
                            </div>
                            <div class="col-lg-12 col-xl-12">
                                <div class="form-floating">
                                    <input type="phone" class="form-control" value="{{ old('subject') }}" name="subject"
                                        placeholder="subject Recruited ">
                                    @error('subject')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <label for="phone">subject Recruited *</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" placeholder="Detail of Grievance *" name="message" value="{{ old('message') }}"
                                        style="height: 160px">{{ old('message') }}</textarea>
                                    @error('message')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <label for="message">Grievance Message</label>
                                </div>
                            </div>
                            <div class="">
                                <button class="btn btn-primary btn-lg rounded-1">Sumbit</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
    <!-- Contact End -->

@stop
