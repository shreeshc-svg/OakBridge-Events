    <section class="pricing-section">

        <div class="anim-icons">

            <span class="icon icon-circle-green wow fadeIn"></span>

            <span class="icon icon-circle-blue wow fadeIn"></span>

            <span class="icon icon-circle-pink wow fadeIn"></span>

        </div>



        <div id="tickets" class="auto-container">

            <div class="sec-title text-center">

                @php $programme = \App\Support\PageContent::get('home.programme'); @endphp
                <span class="title">{{ $programme['eyebrow'] ?: $setting->bname }}</span>

                <h2>{{ $programme['heading'] }}</h2>

            </div>

            <div class="auto-container">
                <div class="row justify-content-center clearfix">
                    @foreach ($services as $service)
                        <div class="contact-column col-md-8 mb-4">
                            <div class="card rounded-1 overflow-hidden">
                                 <img src="{{ asset('public/uploads/images/service/' . $service->image) }}" alt="">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div style="color:#666666">
                                            <h4>{{ $service->title }}</h4>
                                            <span class="icon far fa-clock"></span>
                                            {{ \Carbon\Carbon::parse($service->date)->format('H:i D, d M Y ') }}
                                            </span>
                                        </div>
                                        <span style="color:#666666"><i class="icon flaticon-map"></i> New Delhi</span>
                                    </div>
                                    <p>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                    </p>
                                    <p>{{ Str::limit($service->excerpt, 150) }}</p>
                                    <p></p>
                                    <div class="d-flex justify-content-between px-1">

                                        <div class="btn-box w-100">

                                            <a href="{{ route('service.detail', $service->slug) }}"
                                                class="theme-btn btn-style-one w-100"><span class="btn-title">View Schedule</span></a>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

    </section>
