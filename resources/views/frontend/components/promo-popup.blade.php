{{-- Marketing popup. Managed in Admin > Marketing Popup; shows once per browser session. --}}
@php
    $PB = \App\Support\PromoBanner::class;
    // a validation error reopens the registration modal - never stack two popups
    $promoLive = $PB::isLive($setting ?? null) && ! $errors->any();
@endphp

@if ($promoLive)
    @php
        $promoFile = $setting->promo_media;
        $promoMobileFile = $setting->promo_media_mobile ?: null;
        $promoUrl = $PB::url($promoFile);
        $promoMobileUrl = $PB::url($promoMobileFile);
        $promoLink = $setting->promo_link;
        $promoAlt = $setting->promo_alt ?: 'Announcement';
        $promoKey = 'ob_promo_v' . (int) ($setting->promo_version ?? 1);
    @endphp

    <div class="ob-promo" id="obPromo" role="dialog" aria-modal="true" aria-label="{{ $promoAlt }}"
        data-has-mobile="{{ $promoMobileUrl ? '1' : '0' }}" hidden>
        <div class="ob-promo__backdrop" data-promo-close></div>

        <div class="ob-promo__panel" style="--ob-promo-max: {{ $PB::maxWidthCss($setting) }}">
            <button type="button" class="ob-promo__close" data-promo-close aria-label="Close">&times;</button>

            @php
                $promoTag = $promoLink ? 'a' : 'span';
                $promoAttrs = $promoLink
                    ? ' href="' . e($promoLink) . '"' . ($setting->promo_new_tab ? ' target="_blank" rel="noopener noreferrer"' : '')
                    : '';
            @endphp
            <{{ $promoTag }} class="ob-promo__media"{!! $promoAttrs !!}>

            @if ($PB::isVideo($promoFile))
                <video class="ob-promo__file ob-promo__file--desktop" autoplay muted loop playsinline preload="auto">
                    <source src="{{ $promoUrl }}" type="{{ $PB::mimeType($promoFile) }}">
                </video>
            @else
                <img class="ob-promo__file ob-promo__file--desktop" src="{{ $promoUrl }}" alt="{{ $promoAlt }}"
                    @if ($setting->promo_width) width="{{ $setting->promo_width }}" height="{{ $setting->promo_height }}" @endif
                    decoding="async">
            @endif

            @if ($promoMobileUrl)
                @if ($PB::isVideo($promoMobileFile))
                    <video class="ob-promo__file ob-promo__file--mobile" autoplay muted loop playsinline preload="auto">
                        <source src="{{ $promoMobileUrl }}" type="{{ $PB::mimeType($promoMobileFile) }}">
                    </video>
                @else
                    <img class="ob-promo__file ob-promo__file--mobile" src="{{ $promoMobileUrl }}" alt="{{ $promoAlt }}"
                        decoding="async">
                @endif
            @endif

            </{{ $promoTag }}>

            @if ($setting->promo_dismissible)
                <label class="ob-promo__again">
                    <input type="checkbox" id="obPromoAgain"> Don't show this again
                </label>
            @endif
        </div>
    </div>

    <style>
        .ob-promo { position: fixed; inset: 0; z-index: 20000; display: flex; align-items: center;
            justify-content: center; padding: 1rem; }
        .ob-promo[hidden] { display: none; }
        .ob-promo__backdrop { position: absolute; inset: 0; background: rgba(0, 0, 0, .68); }
        .ob-promo__panel { position: relative; max-width: var(--ob-promo-max, min(92vw, 760px));
            max-height: 92vh; border-radius: .5rem; overflow: visible;
            animation: obPromoIn .28s ease-out both; }
        @keyframes obPromoIn { from { opacity: 0; transform: translateY(14px) scale(.97); } }
        @media (prefers-reduced-motion: reduce) { .ob-promo__panel { animation: none; } }
        .ob-promo__media { display: block; border-radius: .5rem; overflow: hidden; line-height: 0;
            background: #fff; box-shadow: 0 18px 50px rgba(0, 0, 0, .45); }
        a.ob-promo__media { cursor: pointer; }
        .ob-promo__file { display: block; width: 100%; height: auto; max-height: 86vh;
            object-fit: contain; image-rendering: auto; }
        .ob-promo__file--mobile { display: none; }
        .ob-promo__close { position: absolute; top: -14px; right: -14px; z-index: 2; width: 36px; height: 36px;
            border-radius: 50%; border: 0; background: #fff; color: #222; font-size: 24px; line-height: 1;
            cursor: pointer; box-shadow: 0 2px 10px rgba(0, 0, 0, .35); }
        .ob-promo__close:hover { background: #f1f1f1; }
        .ob-promo__close:focus-visible { outline: 3px solid #b8860b; outline-offset: 2px; }
        .ob-promo__again { display: block; margin: .65rem 0 0; text-align: center; color: #fff;
            font-size: .82rem; font-weight: 400; cursor: pointer; }
        .ob-promo__again input { margin-right: .35rem; vertical-align: middle; }
        @media (max-width: 767px) {
            .ob-promo__panel { max-width: 94vw; }
            .ob-promo__close { top: -12px; right: -8px; }
            .ob-promo[data-has-mobile="1"] .ob-promo__file--desktop { display: none; }
            .ob-promo[data-has-mobile="1"] .ob-promo__file--mobile { display: block; }
        }
    </style>

    <script>
        (function () {
            var popup = document.getElementById('obPromo');
            if (!popup) return;

            // an ancestor with a transform (the page animations) would trap a fixed
            // position inside itself, so the overlay lives directly on <body>
            if (popup.parentNode !== document.body) document.body.appendChild(popup);

            var KEY = @json($promoKey);
            var DELAY = {{ (int) ($setting->promo_delay ?? 3) * 1000 }};
            var again = document.getElementById('obPromoAgain');
            var lastFocus = null;

            // storage is unavailable in some private modes - never let that stop the page
            function readFlag(store, key) {
                try { return window[store].getItem(key) === '1'; } catch (e) { return false; }
            }
            function writeFlag(store, key) {
                try { window[store].setItem(key, '1'); } catch (e) { /* ignore */ }
            }

            // dismissed for good, or already seen this session
            if (readFlag('localStorage', KEY + '_never') || readFlag('sessionStorage', KEY + '_seen')) {
                return;
            }

            function open() {
                lastFocus = document.activeElement;
                popup.hidden = false;
                document.body.style.overflow = 'hidden';   // don't let the page scroll behind it
                writeFlag('sessionStorage', KEY + '_seen');
                var closer = popup.querySelector('.ob-promo__close');
                if (closer) closer.focus();
            }

            function close() {
                if (again && again.checked) writeFlag('localStorage', KEY + '_never');
                popup.hidden = true;
                document.body.style.overflow = '';
                document.removeEventListener('keydown', onKey);
                if (lastFocus && lastFocus.focus) lastFocus.focus();
            }

            function onKey(event) {
                if (event.key === 'Escape' || event.key === 'Esc') close();
            }

            popup.querySelectorAll('[data-promo-close]').forEach(function (el) {
                el.addEventListener('click', close);
            });
            // following the link counts as closing it
            var link = popup.querySelector('a.ob-promo__media');
            if (link) link.addEventListener('click', function () { setTimeout(close, 60); });

            window.setTimeout(function () {
                document.addEventListener('keydown', onKey);
                open();
            }, DELAY);
        })();
    </script>
@endif
