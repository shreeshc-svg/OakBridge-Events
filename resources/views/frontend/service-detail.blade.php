@extends('frontend.layouts.app')
@section('title', $service->title . ' ' . '-' . ' ' . $setting->site_title)
@section('keywords', $setting->site_keywords)
@section('description', $setting->site_description)
@section('content')



    <!--Page Title-->
    {{-- <section class="page-title" style="background-image:url({{ asset('public/assets/images/background/5.jpg') }});"> --}}
    <section class="page-title"
        style="background-image: url({{ asset('public/assets/images/background/bread2.webp') }}); background-size: cover; background-position: center;">
        <div class="auto-container">
            <h1>{{ $service->title }}</h1>
            <ul class="bread-crumb clearfix">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li>{{ $service->title }}</li>
            </ul>
        </div>
    </section>
    <!--End Page Title-->
    <section class="event-detail">

        <div class="auto-container">

            <div class="image-box">

              {{--  <figure class="image wow fadeIn animated" style="visibility: visible; animation-name: fadeIn;"><a
                        href="images/resource/event-detail.jpg" class="lightbox-image"><img
                            src="{{ asset('public/uploads/images/service/' . $service->image) }}" alt=""></a>
                </figure>--}}

            </div> 



            <div class="content-box">
                <ul class="upper-info">

                    <li><span
                            class="icon far fa-clock"></span>{{ \Carbon\Carbon::parse($service->date)->format('H:i D, d M Y ') }}
                        </span>

                    </li>

                    <li><span class="icon fa fa-map-marker-alt"></span>{{ $setting->address }}</li>

                </ul>

               {{-- <h2>{{ $service->title }}</h2>

                <p style="color:#666666 font-size:16px">{!! $service->body !!}</p>  --}}
                

                {{-- download pdf option here --}}
                {{-- <div class="btn-box w-100 text-center">
                    <a href="#" class="theme-btn btn-style-three"><span class="btn-title">Download Schedule</span></a>
                </div> --}}


            </div>


        </div>


    </section>

    <section class="schedule-section">

        <div class="anim-icons">

            <span class="icon icon-circle-4 wow zoomIn" style="visibility: hidden; animation-name: none;"></span>

            <span class="icon icon-circle-3 wow zoomIn animated"
                style="visibility: visible; animation-name: zoomIn;"></span>

        </div>



        <div class="auto-container">

            <div class="sec-title text-center">

                <span class="title">Event Agenda</span>

                <h2>Schedule</h2>

            </div>



            <div class="schedule-tabs tabs-box">




                <div class="tabs-content">



                    <!--Tab-->

                    <div class="tab active-tab" id="tab-1">

                        <div class="schedule-timeline">

                            <!-- schedule Block -->

                            @foreach (json_decode($service->timeline, true) as $timeline)
                                <div class="schedule-block @if ($loop->even) even @endif">

                                    <div class="inner-box">

                                        <div class="inner">

                                            <div class="date">{{ $timeline['from'] }} <br> {{ $timeline['to'] }}</div>

                                            <div class="speaker-info">



                                                <h5 class="name">{{ $timeline['title'] }}</h5>

                                                <span class="designation">{{ $timeline['subheadline'] }} </span>

                                            </div>


                                            {{-- <div class="text pt-3">{{ $timeline['body'] }}</div>
                                            
                                            
                                                                                      
                                            <div class="text pt-3">{!! preg_replace('/;\s*/', '<br>–', $timeline['body']) !!}</div> --}}
                                            
{{-- <div class="text pt-3">
    {!! preg_replace('/^–\s*/', '', preg_replace('/;?\s*([^;]+?),\s*(.*?)(?=(;|$))/', '– $1, <i>$2</i><br>', $timeline['body'])) !!}
</div> --}}

<div class="text pt-3">
    @php
        $startsWithDash = strpos($timeline['body'], '–') === 0;
        
        $formatted = preg_replace_callback('/;?\s*([^;]+?),\s*(.*?)(?=(;|$))/', function($matches) {
            $title = $matches[1];
            $description = $matches[2];
            
            // Don't italicize if it contains "DIGITAL"
            if (strpos($description, 'DIGITAL') !== false || strpos($description, 'Driving Innovation') !== false) {
                return '– ' . $title . ', ' . $description . '<br>';
            }
            
            return '– ' . $title . ', <i>' . $description . '</i><br>';
        }, $timeline['body']);
        
        // Always trim the generated dash
        $formatted = ltrim($formatted, '– ');
        
        // Add it back if original had it
        if ($startsWithDash) {
            $formatted = '– ' . $formatted;
        }
    @endphp
    {!! $formatted !!}
</div>


                                        </div>

                                    </div>

                                </div>
                            @endforeach

                        </div>

                    </div>






                </div>

            </div>

        </div>
        
        <div class="btn-box w-100 text-center">

                    <a href="#" data-toggle="modal" data-target="#exampleModal" class="theme-btn btn-style-one"><span
                            class="btn-title">Register Now</span></a>
                            
                      <a href="/public/uploads/Agenda_29_Nov.pdf" download 
       class="theme-btn btn-style-three ml-3">
        <span class="btn-title">Download Agenda</span>
    </a>

                </div>

    </section>

    {{-- <section class="call-to-action" style="background-image: url({{ asset('public/assets/images/background/11.jpg') }});">

        <div class="auto-container">

            <div class="content-box">

                <div class="text">Join Us at Vidhi Utsav 2025! </div>

                <h2>Discover the intersection of law, literature, and knowledge at Vidhi Utsav!</h2>

                <div class="btn-box">

                    <a href="#" data-toggle="modal" data-target="#exampleModal" class="theme-btn btn-style-one"><span
                            class="btn-title">Register Now</span></a>

                </div>
                 
                 
                

            </div>

        </div>

    </section> --}}



@stop

@section('js')

@stop
