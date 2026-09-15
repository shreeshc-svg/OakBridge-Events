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
    <section class="ob-partners" id="sponsors">
        <div class="auto-container">
            <div class="ob-partners__head">
                <span class="ob-partners__eyebrow">Our partners</span>
                <h2>{{ $sponsorsHeading }}</h2>
            </div>

            <div class="ob-partners__tiers">
                @foreach ($sponsorGroups as $group)
                    @php
                        // spread logos evenly over rows (e.g. 6 -> 3 + 3, 7 -> 4 + 3) instead of leaving one on its own
                        $logoCount = $group->activeSponsors->count();
                        $maxPerRow = ['large' => 3, 'medium' => 4, 'small' => 5][$group->logo_size] ?? 5;
                        $perRow = (int) ceil($logoCount / max(1, (int) ceil($logoCount / $maxPerRow)));
                    @endphp
                    <div class="ob-tier ob-tier--{{ $group->logo_size }}" style="--per-row: {{ $perRow }}" @if ($group->slug) id="{{ $group->slug }}" @endif>
                        <h3 class="ob-tier__label"><span>{{ $group->title }}</span></h3>
                        <div class="ob-tier__logos">
                            @foreach ($group->activeSponsors as $sponsor)
                                @if ($sponsor->url)
                                    <a class="ob-logo" href="{{ $sponsor->url }}" target="_blank" rel="noopener" title="{{ $sponsor->name }}">
                                @else
                                    <div class="ob-logo" title="{{ $sponsor->name }}">
                                @endif
                                    <img src="{{ \App\Support\Uploads::url($sponsor->logo) }}" alt="{{ $sponsor->name }}" loading="lazy">
                                @if ($sponsor->url)
                                    </a>
                                @else
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
