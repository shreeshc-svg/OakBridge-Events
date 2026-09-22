@extends('frontend.layouts.app')
@section('title', $setting->bname . ' ' . '-' . '' . $setting->site_title)
@section('keywords', $setting->site_keywords)
@section('description', $setting->site_description)
@section('content')
    <!-- Banner Section -->

    {{-- <section class="banner-section">

        <div class="banner-carousel owl-carousel owl-theme">

            <!-- Slide Item -->

            <div class="slide-item" style="background-image: url({{ asset('public/assets/images/main-slider/1.jpg') }});">

                <div class="auto-container">

                    <div class="content-box">

                        <span class="title">February 21, 2025</span>

                        <h2> Vidhi <br>Utsav 2025</h2>

                        <ul class="info-list">

                            <li><span class="icon fa fa-chair"></span> 1000 Seats</li>

                            <li><span class="icon fa fa-user-alt"></span> 50 SPEAKERS</li>

                            <li><span class="icon fa fa-map-marker-alt"></span> New Delhi</li>

                        </ul>

                        <div class="btn-box"><a href="#" data-toggle="modal" data-target="#exampleModal"
                                class="theme-btn btn-style-two"><span class="btn-title">Book
                                    Now</span></a></div>

                    </div>

                </div>

            </div>



            <!-- Slide Item -->

            <div class="slide-item" style="background-image: url({{ asset('public/assets/images/main-slider/2.jpg') }});">

                <div class="auto-container">

                    <div class="content-box">

                        <span class="title">February 22, 2025</span>

                        <h2> Vidhi <br>Utsav 2025</h2>

                        <ul class="info-list">

                            <li><span class="icon fa fa-chair"></span> 1000 Seats</li>

                            <li><span class="icon fa fa-user-alt"></span> 50 SPEAKERS</li>

                            <li><span class="icon fa fa-map-marker-alt"></span> New Delhi</li>

                        </ul>

                        <div class="btn-box"><a href="#" data-toggle="modal" data-target="#exampleModal"
                                class="theme-btn btn-style-two"><span class="btn-title">Book
                                    Now</span></a></div>

                    </div>

                </div>

            </div>

        </div>

    </section> --}}

    {{-- Every section below is shown, hidden and ordered in Admin > Page Content.
         The markup for each one lives in resources/views/frontend/sections/home/. --}}
    @foreach (\App\Support\SiteSections::homeOrder() as $obSection)
        @if (\App\Support\SiteSections::isVisible($obSection))
            @include('frontend.sections.home.' . \App\Support\SiteSections::homeView($obSection))
        @endif
    @endforeach

    {{-- Marketing popup (Admin > Marketing Popup) --}}
    @include('frontend.components.promo-popup')

@endsection



@section('js')

    {{-- popup trigger code here --}}
    <script>
        $(document).ready(function() {
            // Check if the user has already visited the "Thanks" page
            var formSubmitted = localStorage.getItem('form_submitted');

           {{-- if (!formSubmitted) {
                // Trigger the modal after 30 seconds
                setTimeout(function() {
                    $('#exampleModal').modal('show');
                }, 30000); // 30000 milliseconds = 30 seconds
            }  --}}

            // Store form submission status when the form is submitted
            $('form').on('submit', function() {
                localStorage.setItem('form_submitted', 'true');
            });
        });
    </script>
@stop
