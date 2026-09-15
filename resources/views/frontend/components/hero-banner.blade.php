{{-- Hero banner (image or video): Admin > Hero Banner --}}
@php
    $heroEnabled = $setting->hero_enabled ?? true;
    $heroClick = $setting->hero_click ?? 'register';
    $heroDesktop = !empty($setting->hero_image) ? $setting->hero_image : null;
    $heroMobileFile = !empty($setting->hero_image_mobile) ? $setting->hero_image_mobile : null;
    $heroAlt = $setting->hero_alt ?? '';
    $heroPoster = !empty($setting->hero_poster) ? \App\Support\HeroMedia::url($setting->hero_poster) : null;
    $heroOpensForm = $heroClick === 'register' && $registrationOpen;
    $heroLink = $heroClick === 'link' && !empty($setting->hero_link) ? $setting->hero_link : null;
    $heroHasVideo = \App\Support\HeroMedia::isVideo($heroDesktop) || \App\Support\HeroMedia::isVideo($heroMobileFile);
    $modalAttrs = $heroOpensForm ? 'style="cursor: pointer" data-toggle="modal" data-target="#exampleModal"' : '';
@endphp
<section class="pt-5 pt-sm-0">
    @if ($heroEnabled)
        @if ($heroLink)
            <a href="{{ $heroLink }}" @if ($setting->hero_new_tab) target="_blank" rel="noopener" @endif>
        @endif

        @if (! $heroHasVideo)
            {{-- image only (with an optional separate phone image) --}}
            <picture>
                @if ($heroMobileFile)
                    <source media="(max-width: 575.98px)" srcset="{{ \App\Support\HeroMedia::url($heroMobileFile) }}">
                @endif
                <img class="pt-4 pt-sm-0 img-fluid w-100"
                    src="{{ $heroDesktop ? \App\Support\HeroMedia::url($heroDesktop) : asset(\App\Support\HeroMedia::DEFAULT_IMAGE) }}"
                    alt="{{ $heroAlt }}" {!! $modalAttrs !!}>
            </picture>
        @else
            {{-- at least one video: desktop and phone versions are separate elements, shown by screen size --}}
            @php
                $heroItems = [];
                if ($heroMobileFile) {
                    $heroItems[] = ['file' => $heroDesktop, 'class' => 'd-none d-sm-block'];
                    $heroItems[] = ['file' => $heroMobileFile, 'class' => 'd-sm-none'];
                } else {
                    $heroItems[] = ['file' => $heroDesktop, 'class' => ''];
                }
            @endphp
            @foreach ($heroItems as $item)
                @if (\App\Support\HeroMedia::isVideo($item['file']))
                    <video class="ob-hero-video pt-4 pt-sm-0 w-100 {{ $item['class'] }}" muted loop playsinline preload="none"
                        data-src="{{ \App\Support\HeroMedia::url($item['file']) }}" data-type="{{ \App\Support\HeroMedia::mimeType($item['file']) }}"
                        @if ($heroPoster) poster="{{ $heroPoster }}" @endif
                        aria-label="{{ $heroAlt }}" {!! $modalAttrs !!}></video>
                @else
                    <img class="pt-4 pt-sm-0 img-fluid w-100 {{ $item['class'] }}"
                        src="{{ $item['file'] ? \App\Support\HeroMedia::url($item['file']) : asset(\App\Support\HeroMedia::DEFAULT_IMAGE) }}"
                        alt="{{ $heroAlt }}" {!! $modalAttrs !!}>
                @endif
            @endforeach
            <script>
                (function() {
                    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                    // load only the video that is visible at this screen size
                    document.querySelectorAll('video.ob-hero-video[data-src]').forEach(function(video) {
                        if (getComputedStyle(video).display === 'none') { return; }
                        var source = document.createElement('source');
                        source.src = video.dataset.src;
                        source.type = video.dataset.type;
                        video.appendChild(source);
                        video.removeAttribute('data-src');
                        if (reduceMotion) {
                            video.preload = 'metadata';
                            video.load();
                            return;
                        }
                        video.muted = true; // required for autoplay on iPhone / Safari
                        video.autoplay = true;
                        video.preload = 'auto';
                        video.load();
                        var playing = video.play();
                        if (playing && playing.catch) { playing.catch(function() {}); }
                    });
                })();
            </script>
            <style>
                .ob-hero-video { display: block; height: auto; background: #1b1340; background-clip: content-box; }
            </style>
        @endif

        @if ($heroLink)
            </a>
        @endif
    @endif
</section>
