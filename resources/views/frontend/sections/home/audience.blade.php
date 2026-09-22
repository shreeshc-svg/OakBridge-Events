{{-- Who should attend. Text: Admin > Page Content > Homepage. Shown, hidden and ordered in Admin > Page Content. --}}
    @php $audience = \App\Support\PageContent::get('home.audience'); @endphp
    <section class="why-choose-us">
        <div class="auto-container">
            <div class="row align-items-center">
                <div class="content-column col-lg-6 col-md-12 col-sm-12 ">
                    <div class="inner-column">
                        <div class="sec-title">
                            @if ($audience['eyebrow'])
                                <span class="title">{{ $audience['eyebrow'] }}</span>
                            @endif
                            <h2>{{ $audience['heading'] }}</h2>
                        </div>
                        @if (count($audience['items']))
                            <ul class="list-style-one">
                                @foreach ($audience['items'] as $line)
                                    <li>{{ $line['text'] }}</li>
                                @endforeach
                            </ul>
                        @endif
                        @if ($registrationOpen && $audience['button_label'])
                            <div class="btn-box"><a href="#" data-toggle="modal" data-target="#exampleModal"
                                    class="theme-btn btn-style-three"><span class="btn-title">{{ $audience['button_label'] }}</span></a></div>
                        @endif
                    </div>
                </div>
                @if ($audience['image'])
                    <div class="image-column col-lg-6 col-md-12 col-sm-12 ">
                        <div class="image-box">
                            <figure class="image"><img src="{{ \App\Support\Uploads::url($audience['image']) }}" alt="{{ $audience['heading'] }}">
                            </figure>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
