@extends('frontend.layouts.app')
@section('title', 'Register | India Law, AI & Tech Summit')
@section('keywords', 'india law ai tech summit registration, legal tech conference tickets, delegate pass')
@section('description', 'Register for the India Law, AI & Tech Summit: choose your pass and book your seat in a couple of minutes.')
@section('content')
    {{-- No banner: the page opens straight on the form so nothing distracts from registering.
         Title, subtitle and heading: Admin > Page Content > Registration page --}}
    <section class="ob-register">
        <div class="auto-container">
            <div class="ob-register__card" id="register-form">
                @include('frontend.components.register-heading', ['tag' => 'h2', 'titleTag' => 'h1'])
                @include('frontend.components.booking-form')
            </div>
        </div>
    </section>
@stop

@section('js')
    @unless ($errors->any())
        <script>
            // desktop: start typing straight away (phones skip it, so the keyboard doesn't cover the form)
            document.addEventListener('DOMContentLoaded', function () {
                if (window.matchMedia('(min-width: 768px)').matches) {
                    var name = document.querySelector('#registerForm input[name=name]');
                    if (name && !name.value) { name.focus({ preventScroll: true }); }
                }
            });
        </script>
    @endunless
    @if ($errors->any())
        <script>
            // back from a failed submit: bring the form (and its messages) into view
            document.addEventListener('DOMContentLoaded', function () {
                document.getElementById('register-form').scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        </script>
    @endif
@stop
