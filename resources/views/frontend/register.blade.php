@extends('frontend.layouts.app')
@section('title', 'Register | India Law, AI & Tech Summit')
@section('keywords', 'india law ai tech summit registration, legal tech conference tickets, delegate pass')
@section('description', 'Register for the India Law, AI & Tech Summit: choose your pass and book your seat in a couple of minutes.')
@section('content')
    {{-- Title, subtitle and form heading: Admin > Page Content > Registration page --}}
    <section class="page-title ob-register-title"
        style="background-image: url({{ asset('public/assets/images/background/bread2.webp') }}); background-size: cover; background-position: center;">
        <div class="auto-container">
            <h1>{{ $page['title'] }}</h1>
            @if ($page['subtitle'])
                <p class="ob-register-title__sub">{{ $page['subtitle'] }}</p>
            @endif
        </div>
    </section>

    <section class="ob-register">
        <div class="auto-container">
            <div class="ob-register__card" id="register-form">
                @if ($page['form_heading'])
                    <h2 class="ob-register__heading">{{ $page['form_heading'] }}</h2>
                @endif
                @if ($page['note'])
                    <p class="ob-register__note">{!! nl2br(e($page['note'])) !!}</p>
                @endif
                @include('frontend.components.booking-form')
            </div>
        </div>
    </section>
@stop

@section('js')
    @if ($errors->any())
        <script>
            // back from a failed submit: bring the form (and its messages) into view
            document.addEventListener('DOMContentLoaded', function () {
                document.getElementById('register-form').scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        </script>
    @endif
@stop
