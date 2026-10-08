{{--
    Year switcher under a page title. Needs: $section (speakers|sponsors|images|videos), $edition (current year or null).
    On the gallery pages it also switches between Images and Videos of the same year.
--}}
@php
    $tabYears = \App\Support\Editions::years($section);
    $tabRoute = ['speakers' => 'speakers', 'sponsors' => 'sponsors', 'images' => 'gallery', 'videos' => 'videos'][$section];
    $currentRow = $edition ? \App\Support\Editions::index()->first(fn ($row) => $row['edition']->id === $edition->id) : null;
    $isGallery = in_array($section, ['images', 'videos'], true);
@endphp
@if ($edition && ($tabYears->count() > 1 || ($isGallery && $currentRow && $currentRow['images'] && $currentRow['videos'])))
    <div class="ob-year-tabs" role="navigation" aria-label="Choose a year">
        <div class="auto-container">
            @if ($tabYears->count() > 1)
                <div class="ob-year-tabs__row">
                    @foreach ($tabYears as $row)
                        <a href="{{ route($tabRoute, $row['edition']->year) }}"
                            class="ob-year-tabs__pill {{ $row['edition']->id === $edition->id ? 'is-active' : '' }}"
                            @if ($row['edition']->id === $edition->id) aria-current="page" @endif>{{ $row['edition']->year }}</a>
                    @endforeach
                </div>
            @endif
            @if ($isGallery && $currentRow && $currentRow['images'] && $currentRow['videos'])
                <div class="ob-year-tabs__row ob-year-tabs__row--kind">
                    <a href="{{ route('gallery', $edition->year) }}"
                        class="ob-year-tabs__kind {{ $section === 'images' ? 'is-active' : '' }}">Images</a>
                    <a href="{{ route('videos', $edition->year) }}"
                        class="ob-year-tabs__kind {{ $section === 'videos' ? 'is-active' : '' }}">Videos</a>
                </div>
            @endif
        </div>
    </div>
@endif
