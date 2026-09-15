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
            <h1>Legathon</h1>
            <ul class="bread-crumb clearfix">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li>Legathon</li>
            </ul>
        </div>
    </section>
    <!--End Page Title-->


    <section class="py-5">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-md-7 order-2 order-md-1">
                    <div class="sec-title">
                        <h2>Legislation Drafting Competition</h2>
                        <p class="title mb-0 mt-2">Fali Nariman Memorial Legathon</p>
                        <div class="text mb-2">Participants are invited to draft a legislation titled – <strong>“Public
                                Service
                                Efficiency Act: Standards, Metrics, and Enforcement”</strong>
                        </div>
                        <p>
                            The proposed legislation should:
                        </p>

                        <ul class="list-style-one">

                            <li>Identify core public services (e.g., healthcare, education, infrastructure maintenance,
                                water, and sanitation) that require standardized service delivery metrics.</li>

                            <li>Define clear, measurable performance standards and timelines for these services.
                            </li>

                            <li>Incorporate an accountability structure, outlining the roles of agencies in monitoring and
                                enforcing standards.</li>

                            <li>Propose penalties and incentives to encourage compliance and maintain service quality.</li>

                        </ul>

                        <div class="d-md-flex mt-4">
                            <div class="btn-box mr-3 ">
                                <a target="_blank" href="{{ asset('public/assets/legathan/fali-nariman.pdf') }}"
                                    class="theme-btn btn-style-one px-3 py-1"><span class="btn-title">Learn More</span></a>

                            </div>

                            <div class="btn-box mr-3">
                                <a target="_blank"
                                    href="https://docs.google.com/forms/d/e/1FAIpQLSepNNESudMWJNgjsSA680K6I73DZtgCJfUqYnEDzyEh7aMd9g/viewform"
                                    class="theme-btn btn-style-two px-3 py-1"><span class="btn-title">Click to
                                        Register</span></a>

                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-md-5 order-1 order-md-2">
                    <img class="img-fluid" src="{{ asset('public/assets/legathan/drafting-image.webp') }}" alt="">
                </div>
            </div>
        </div>
    </section>





    <section class="py-5">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-md-7 order-2 order-md-1">
                    <div class="sec-title">


                        <h2>Lights, Camera & Justice </h2>
                        <p class="title mb-0 mt-2">Short Video Making Competition</p>




                        <div class="text mb-2">Participants are invited to create a short video that delves into one of the
                            following sub-themes:
                        </div>

                        <ul class="list-style-one">

                            <li>Judicial Activism in India</li>

                            <li>Free Speech in the Digital Age:
                                Challenges in India
                            </li>

                            <li>Role of Law & Justice in Improving Accessibility of Justice</li>

                            <li>Consumer Law Issues</li>
                            <li>Role of AI in Law</li>
                            <li>Justice in Couplets:
                                Shayari mein Kanoon ki Kahani
                            </li>


                        </ul>

                        <div class="d-md-flex mt-4">
                            <div class="btn-box mr-3 ">
                                <a target="_blank" href="{{ asset('public/assets/legathan/light-camera-action.pdf') }}"
                                    class="theme-btn btn-style-one px-3 py-1"><span class="btn-title">Learn More</span></a>

                            </div>

                            <div class="btn-box mr-3">
                                <a target="_blank"
                                    href="https://docs.google.com/forms/d/e/1FAIpQLSepNNESudMWJNgjsSA680K6I73DZtgCJfUqYnEDzyEh7aMd9g/viewform"
                                    class="theme-btn btn-style-two px-3 py-1"><span class="btn-title">Click to
                                        Register</span></a>

                            </div>

                            <div class="btn-box">
                                <a target="_blank" href="https://www.youtube.com/watch?v=sCBnF0DXqp8"
                                    class="theme-btn btn-style-three px-3 py-1"><span class="btn-title">Workshop by Aditya
                                        Bhasin</span></a>

                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-md-5 order-1 order-md-2">
                    <img class="img-fluid" src="{{ asset('public/assets/legathan/video-image.webp') }}" alt="">
                </div>
            </div>
        </div>
    </section>



    {{-- videos section --}}

    {{-- <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="sec-title mb-3 pl-md-2 mx-auto">
                    <h2>Videos</h2>
                </div>
            </div>
            <div class="row">


                <div class="col-md-4 mb-5">
                    <div class="card p-2">
                        <iframe width="100%" height="220" src="https://www.youtube.com/embed/AETFvQonfV8"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen="">
                        </iframe>
                        <h4 class="text-center p-1">Hanuman JI</h4>
                    </div>
                </div>


                <div class="col-md-4 mb-5">
                    <div class="card p-2">
                        <iframe width="100%" height="220" src="https://www.youtube.com/embed/AETFvQonfV8"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen="">
                        </iframe>
                        <h4 class="text-center p-1">Hanuman Chalisa</h4>
                    </div>
                </div>


                <div class="col-md-4 mb-5">
                    <div class="card p-2">
                        <iframe width="100%" height="220" src="https://www.youtube.com/embed/XA25Us9fTww"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen="">
                        </iframe>
                        <h4 class="text-center p-1">Ram Nam</h4>
                    </div>
                </div>



            </div>

            <div class="row text-center">
                <div class="btn-box mr-3 mx-auto">
                    <a target="_blank" href="https://www.youtube.com/embed/XA25Us9fTww"
                        class="theme-btn btn-style-one px-3 py-1"><span class="btn-title">Other Videos</span></a>

                </div>
            </div>
        </div>
    </section> --}}



@stop

@section('js')

@stop
