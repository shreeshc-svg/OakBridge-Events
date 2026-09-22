{{-- Register section, with the booking form. Shown, hidden and ordered in Admin > Page Content. --}}
@if ($registrationOpen)
    <section class="register-section">

        <div class="auto-container">

            <div class="anim-icons full-width">

                <span class="icon icon-circle-3 wow zoomIn"></span>

            </div>

            <div class="outer-box">

                <div class="row no-gutters">

                    <div class="title-column col-lg-4 col-md-6 col-sm-12">

                        <div class="inner">

                            <div class="sec-title light">

                                <div class="icon-box"><span class="">
                                        <img class="w-50"
                                            src="{{ asset('public/uploads/images/logo/' . $setting->logo) }}"
                                            alt=""></span></div>

                                @php $registerSection = \App\Support\PageContent::get('home.register'); @endphp
                                <h2>{{ $registerSection['heading'] }}</h2>

                                <div class="text">{!! nl2br(e($registerSection['body'])) !!}</div>

                            </div>

                        </div>

                    </div>

                    <!--Register Form-->

                    <div id="booking" class="register-form col-lg-8 col-md-6 col-sm-12">

                        <div class="form-inner">

                            @include('frontend.components.booking-form')

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>
@endif
