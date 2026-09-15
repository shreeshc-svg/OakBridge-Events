{{-- Partners & sponsors: Admin > Sponsors & Exhibitors (heading in Admin > Page Content) --}}
@php
    try {
        $sponsorGroups = \App\Models\SponsorGroup::where('is_active', true)
            ->orderBy('sort_order')->orderBy('id')
            ->with('activeSponsors')
            ->get()
            ->filter(fn ($group) => $group->activeSponsors->count());
    } catch (\Throwable $e) {
        $sponsorGroups = collect();
    }
    $sponsorsHeading = \App\Support\PageContent::get('home.sponsors')['heading'];
@endphp
@if ($sponsorGroups->count())
    <section class="clients-section">
        <div class="anim-icons">
            <span class="icon icon-dots-3 wow zoomIn"></span>
            <span class="icon icon-circle-blue wow zoomIn"></span>
        </div>
        <div class="auto-container">
            <div style="text-align: center;" class="sec-title mx-auto" id="sponsors">
                <h2>{{ $sponsorsHeading }}</h2>
            </div>
            <div class="sponsors-outer">
                @foreach ($sponsorGroups as $group)
                    <div class="row" @if ($group->slug) id="{{ $group->slug }}" @endif>
                        <h2 class="pb-3 mx-auto" style="color: #666666">{{ $group->title }}</h2>
                    </div>
                    <div class="row justify-content-center align-items-center">
                        @foreach ($group->activeSponsors as $sponsor)
                            <div class="client-block {{ $group->columnClass() }}">
                                <figure class="image-box">
                                    @if ($sponsor->url)
                                        <a href="{{ $sponsor->url }}" target="_blank" rel="noopener">
                                    @else
                                        <a href="javascript:void(0)">
                                    @endif
                                        <img class="img-fluid" src="{{ \App\Support\Uploads::url($sponsor->logo) }}"
                                            alt="{{ $sponsor->name }}">
                                    </a>
                                </figure>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
