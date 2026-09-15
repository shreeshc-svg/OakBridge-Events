@extends('adminlte::page')

@section('title', 'Marketing Strip')

@section('content_header')
    <h1>Marketing Strip</h1>
@stop

@section('content')
    @php
        $maxItems = \App\Http\Controllers\MarketingStripController::MAX_ITEMS;
        $maxLen = \App\Http\Controllers\MarketingStripController::MAX_ITEM_LENGTH;
        $speeds = \App\Http\Controllers\MarketingStripController::SPEEDS;
        $items = old('strip_items', $setting->strip_items);
        $bg = old('strip_bg', $setting->strip_bg ?? '#D3181F');
        $fg = old('strip_color', $setting->strip_color ?? '#FFFFFF');
        $speed = old('strip_speed', $setting->strip_speed ?? 'normal');
    @endphp

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Please fix the following:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">Preview</h3>
        </div>
        <div class="card-body p-0">
            <div class="ob-strip" id="stripPreview">
                <div class="ob-strip__track" id="stripTrack"></div>
            </div>
            <p class="text-muted small px-3 py-2 mb-0">
                Shown on the homepage directly below the hero banner. It pauses while a visitor hovers over it.
            </p>
        </div>
    </div>

    <form action="{{ route('strip.update') }}" method="post">
        @csrf
        <div class="row">
            <div class="col-lg-7">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Phrases</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="strip_items">One phrase per line</label>
                            <textarea class="form-control @error('strip_item_list') is-invalid @enderror" name="strip_items"
                                id="strip_items" rows="6" placeholder="Limited Seats&#10;Register Now&#10;New Delhi&#10;29 November"
                                >{{ $items }}</textarea>
                            <small class="form-text text-muted">
                                3–5 short phrases work best. Up to {{ $maxItems }} phrases, {{ $maxLen }} characters each.
                                Shown in capitals, separated by &#10022;.
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Options</h3>
                    </div>
                    <div class="card-body">
                        <div class="custom-control custom-switch mb-3">
                            <input type="hidden" name="strip_enabled" value="0">
                            <input type="checkbox" class="custom-control-input" id="strip_enabled" name="strip_enabled"
                                value="1" {{ old('strip_enabled', $setting->strip_enabled) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="strip_enabled">Show strip on homepage</label>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-6">
                                <label for="strip_bg">Background</label>
                                <input type="color" class="form-control" name="strip_bg" id="strip_bg"
                                    value="{{ $bg }}">
                            </div>
                            <div class="form-group col-6">
                                <label for="strip_color">Text</label>
                                <input type="color" class="form-control" name="strip_color" id="strip_color"
                                    value="{{ $fg }}">
                            </div>
                        </div>
                        <p class="small text-muted mt-n2">
                            Site colours:
                            <a href="#" class="swatch" data-bg="#D3181F" data-fg="#FFFFFF">red</a> ·
                            <a href="#" class="swatch" data-bg="#4C35A9" data-fg="#FFFFFF">purple</a> ·
                            <a href="#" class="swatch" data-bg="#222222" data-fg="#FFFFFF">dark</a>
                        </p>

                        <div class="form-group">
                            <label for="strip_speed">Scroll speed</label>
                            <select class="form-control" name="strip_speed" id="strip_speed">
                                @foreach ($speeds as $key => $px)
                                    <option value="{{ $key }}" data-px="{{ $px }}" {{ $speed === $key ? 'selected' : '' }}>
                                        {{ ucfirst($key) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Save strip</button>
                        <a href="{{ route('home') }}" target="_blank" class="btn btn-outline-secondary btn-block">
                            View homepage
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop

@section('css')
    <style>
        .ob-strip {
            overflow: hidden;
            white-space: nowrap;
            padding: 14px 0;
            font-size: 18px;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            line-height: 1.2;
            min-height: 50px;
        }
        .ob-strip__track { display: flex; width: max-content; animation: ob-strip-scroll linear infinite; }
        .ob-strip:hover .ob-strip__track { animation-play-state: paused; }
        .ob-strip__item { display: inline-flex; align-items: center; }
        .ob-strip__item::after { content: "\2726"; margin: 0 26px; font-size: .8em; opacity: .85; }
        @keyframes ob-strip-scroll { from { transform: translateX(0); } to { transform: translateX(-50%); } }
    </style>
@stop

@section('js')
    <script>
        (function() {
            var textarea = document.getElementById('strip_items');
            var bg = document.getElementById('strip_bg');
            var fg = document.getElementById('strip_color');
            var speed = document.getElementById('strip_speed');
            var strip = document.getElementById('stripPreview');
            var track = document.getElementById('stripTrack');

            function render() {
                var items = textarea.value.split(/\r?\n/).map(function(s) { return s.trim(); })
                    .filter(function(s) { return s !== ''; });
                if (!items.length) { items = ['Your phrase here']; }
                var chars = items.join('').length;
                var setWidth = chars * 12 + items.length * 64;
                var repeat = Math.max(1, Math.ceil(2560 / setWidth));
                var px = parseInt(speed.options[speed.selectedIndex].dataset.px, 10);
                var duration = Math.max(8, Math.round(setWidth * repeat / px));

                track.textContent = '';
                for (var h = 0; h < 2 * repeat; h++) {
                    items.forEach(function(text) {
                        var span = document.createElement('span');
                        span.className = 'ob-strip__item';
                        span.textContent = text;
                        track.appendChild(span);
                    });
                }
                track.style.animationDuration = duration + 's';
                strip.style.backgroundColor = bg.value;
                strip.style.color = fg.value;
            }

            [textarea, bg, fg, speed].forEach(function(el) {
                el.addEventListener('input', render);
                el.addEventListener('change', render);
            });
            document.querySelectorAll('.swatch').forEach(function(a) {
                a.addEventListener('click', function(e) {
                    e.preventDefault();
                    bg.value = a.dataset.bg;
                    fg.value = a.dataset.fg;
                    render();
                });
            });
            render();
        })();
    </script>
@stop
