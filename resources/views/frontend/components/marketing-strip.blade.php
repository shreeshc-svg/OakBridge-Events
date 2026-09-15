{{-- Scrolling marketing strip: managed from Admin > Marketing Strip --}}
@php
    $stripItems = \App\Http\Controllers\MarketingStripController::parseItems($setting->strip_items ?? '');
@endphp
@if (!empty($setting->strip_enabled) && count($stripItems))
    @php
        $speeds = \App\Http\Controllers\MarketingStripController::SPEEDS;
        $pxPerSecond = $speeds[$setting->strip_speed ?? 'normal'] ?? $speeds['normal'];
        // rough width of one set of phrases, used to fill wide screens and keep a steady speed
        $chars = array_sum(array_map('mb_strlen', $stripItems));
        $setWidth = $chars * 12 + count($stripItems) * 64;
        $repeat = max(1, (int) ceil(2560 / max($setWidth, 1)));
        $duration = max(8, (int) round(($setWidth * $repeat) / $pxPerSecond));
        $bg = preg_match('/^#[0-9A-Fa-f]{6}$/', $setting->strip_bg ?? '') ? $setting->strip_bg : '#D3181F';
        $fg = preg_match('/^#[0-9A-Fa-f]{6}$/', $setting->strip_color ?? '') ? $setting->strip_color : '#FFFFFF';
    @endphp
    <style>
        .ob-strip {
            overflow: hidden;
            white-space: nowrap;
            padding: 14px 0;
            font-family: 'Poppins', sans-serif;
            font-size: 18px;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            line-height: 1.2;
        }
        .ob-strip__track {
            display: flex;
            width: max-content;
            animation: ob-strip-scroll linear infinite;
        }
        .ob-strip:hover .ob-strip__track { animation-play-state: paused; }
        .ob-strip__item { display: inline-flex; align-items: center; }
        .ob-strip__item::after {
            content: "\2726";
            margin: 0 26px;
            font-size: .8em;
            opacity: .85;
        }
        .ob-strip__sr {
            position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px;
            overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0;
        }
        @keyframes ob-strip-scroll {
            from { transform: translateX(0); }
            to { transform: translateX(-50%); }
        }
        @media (max-width: 575.98px) {
            .ob-strip { padding: 10px 0; font-size: 15px; }
            .ob-strip__item::after { margin: 0 18px; }
        }
        @media (prefers-reduced-motion: reduce) {
            .ob-strip__track { animation: none; }
        }
    </style>
    <section class="ob-strip" style="background-color: {{ $bg }}; color: {{ $fg }};">
        <ul class="ob-strip__sr">
            @foreach ($stripItems as $item)
                <li>{{ $item }}</li>
            @endforeach
        </ul>
        <div class="ob-strip__track" style="animation-duration: {{ $duration }}s;" aria-hidden="true">
            @for ($half = 0; $half < 2; $half++)
                @for ($r = 0; $r < $repeat; $r++)
                    @foreach ($stripItems as $item)
                        <span class="ob-strip__item">{{ $item }}</span>
                    @endforeach
                @endfor
            @endfor
        </div>
    </section>
@endif
