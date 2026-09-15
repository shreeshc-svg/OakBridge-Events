   <section id="2025" class="speakers-section-three">

        <div class="auto-container">

            <div class="sec-title text-center">

                <span class="title">{{ $setting->bname }}</span>

                <h2>Distinguished Speakers</h2>

            </div>


            <div class="row">

                <!-- Speaker Block -->

                @foreach ($speakers as $speaker)
                    <div class="speaker-block-three col-xl-3 col-lg-4 col-md-6 col-sm-12 wow fadeInUp">

                        <div class="inner-box">

                            <div class="image-box">

                                <figure class="image"><a href="javascript::void"><img
                                            src="{{ asset('public/uploads/images/team/' . $speaker->image) }}"
                                            alt=""></a>
                                </figure>

                            </div>

                            <div class="info-box">

                                <h4 class="name" title="{{ $speaker->name }}"><a href="javascript::void">{{ $speaker->name }}</a></h4>

                                <span class="designation small" title="{{ $speaker->position }}">{{ $speaker->position }}</span>

                            </div>

                            <div class="social-box">

                                <ul class="social-links social-icon-colored">

                                    @if ($speaker->social['facebook'])
                                        <li><a target="_blank" href="{{ $speaker->social['facebook'] }}"><span
                                                    class="fab fa-facebook-f"></span></a>
                                        </li>
                                    @endif

                                    @if ($speaker->social['instagram'])
                                        <li><a target="_blank" href="{{ $speaker->social['instagram'] }}"><span
                                                    class="fab fa-instagram"></span></a>
                                        </li>
                                    @endif


                                    @if ($speaker->social['x'])
                                        <li><a target="_blank" href="{{ $speaker->social['x'] }}"><span
                                                    class="fab fa-twitter"></span></a>
                                        </li>
                                    @endif


                                    @if ($speaker->social['linkedin'])
                                        <li><a target="_blank" href="{{ $speaker->social['linkedin'] }}"><span
                                                    class="fab fa-linkedin-in"></span></a>
                                        </li>
                                    @endif

                                    @if ($speaker->social['youtube'])
                                        <li><a target="_blank" href="{{ $speaker->social['youtube'] }}"><span
                                                    class="fab fa-youtube"></span></a>
                                        </li>
                                    @endif

                                </ul>

                            </div>

                        </div>

                    </div>
                @endforeach


            </div>

        </div>

    </section>
