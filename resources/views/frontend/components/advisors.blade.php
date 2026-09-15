  <section class="speakers-section-two">

        <div class="anim-icons">

            <span class="icon icon-circle-4 wow zoomIn animated" style="visibility: visible; animation-name: zoomIn;"></span>

            <span class="icon icon-circle-3 wow zoomIn animated" style="visibility: visible; animation-name: zoomIn;"></span>

        </div>



        <div class="auto-container">

            <div class="sec-title text-center">

                <span class="title">{{ $setting->bname }}</span>

                <h2>Partners & Sponsors</h2>

            </div>



            <div class="row">
                @foreach ($advisors as $advisor)
                    <div class="speaker-block-two col-xl-3 col-lg-4 col-md-6 col-sm-12 wow fadeInUp animated"
                    style="visibility: visible; animation-name: fadeInUp;">

                    <div class="inner-box h-100">

                        <div class="info-box pb text-center">

                            <h4 class="name"><a href="javascript::void()">{{ $advisor->name }}</a></h4>

                            <span class="designation">{{ $advisor->position }}</span>

                        </div>

                        <div class="image-box">

                            <figure class="image"><a href="javascript::void()"><img
                                        src="{{ asset('public/uploads/images/team/'.$advisor->image) }}"
                                        alt=""></a>
                            </figure>

                        </div>


                    </div>

                </div>
                @endforeach

            </div>

        </div>

    </section>
