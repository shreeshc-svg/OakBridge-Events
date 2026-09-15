{{-- Event overview: Admin > Page Content > Homepage > Event overview --}}
@php $overview = \App\Support\PageContent::get('home.overview'); @endphp
<section class="features-section-two">
    <div class="auto-container">
        <div class="sec-title text-center">
            @if ($overview['eyebrow'])
                <span class="title">{{ $overview['eyebrow'] }}</span>
            @endif
            @if ($overview['heading'])
                <h2>{{ $overview['heading'] }}</h2>
            @endif
            <p></p>
            {{-- the cards sit inside .sec-title (as in the original design) so they stay centred --}}
        <div class="row">
            @foreach ($overview['items'] as $feature)
                <div class="feature-block-two col-lg-4 col-md-6 col-sm-12 wow fadeInUp"
                    @if ($loop->index >= 3) data-wow-delay="{{ ($loop->index - 2) * 400 }}ms" @endif>
                    <div class="inner-box h-100">
                        @if ($feature['icon'])
                            <div class="icon-box"><span class="icon {{ $feature['icon'] }}"></span></div>
                        @endif
                        <h4><a href="javascript:void(0)">{{ $feature['title'] }}</a></h4>
                        <div class="text">{{ $feature['text'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
        </div>
    </div>
</section>
