{{-- Intro and highlight cards. Text: Admin > Page Content > Homepage. Shown, hidden and ordered in Admin > Page Content. --}}
    @php $intro = \App\Support\PageContent::get('home.intro'); @endphp
    <section class="py-5" style="background-color: #f8f9fa;">
        <div class="container">
            <div class="sec-title text-center">
                @if ($intro['eyebrow'])
                    <span class="title">{{ $intro['eyebrow'] }}</span>
                @endif
                @if ($intro['body'])
                    <div class="page-rich-text" style="color:#444444">{!! $intro['body'] !!}</div>
                @endif
            </div>
            @if (count($intro['items']))
                <div class="row g-4">
                    @foreach ($intro['items'] as $card)
                        <div class="col-lg-6 col-md-6 mb-3">
                            <div class="card h-100 p-4 shadow-sm">
                                <div class="text-center">
                                    @if ($card['icon'])
                                        <div class="mb-3" style="font-size: 2.5rem; color: #b8860b;">
                                            <i class="{{ $card['icon'] }}"></i>
                                        </div>
                                    @endif
                                    <h4 class="card-title mb-1" style="color: #333; font-weight: 600;">{{ $card['title'] }}</h4>
                                    <p class="card-text" style="color: #666666">{{ $card['text'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
