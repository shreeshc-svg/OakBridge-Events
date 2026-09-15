@extends('adminlte::page')

@section('title', 'Hero Banner')

@section('content_header')
    <h1>Hero Banner</h1>
@stop

@section('content')
    @php
        $HM = \App\Support\HeroMedia::class;
        $desktopFile = $setting->hero_image;
        $mobileFile = $setting->hero_image_mobile;
        $desktopUrl = $desktopFile ? $HM::url($desktopFile) : asset($HM::DEFAULT_IMAGE);
        $mobileUrl = $mobileFile ? $HM::url($mobileFile) : null;
        $posterUrl = $setting->hero_poster ? $HM::url($setting->hero_poster) : null;
        $click = old('hero_click', $setting->hero_click ?? 'register');
        // what this server accepts in one upload (php.ini)
        $toBytes = function ($v) {
            $v = trim((string) $v); $n = (float) $v; $u = strtolower(substr($v, -1));
            return (int) ($u === 'g' ? $n * 1073741824 : ($u === 'm' ? $n * 1048576 : ($u === 'k' ? $n * 1024 : $n)));
        };
        $serverLimit = min(array_filter([$toBytes(ini_get('upload_max_filesize')), $toBytes(ini_get('post_max_size'))]) ?: [0]);
        $serverLimitMb = $serverLimit ? round($serverLimit / 1048576) : null;
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

    <form action="{{ route('hero.update') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Banner image or video</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-2">
                            Currently showing{{ $desktopFile ? '' : ' (default banner)' }}:
                        </p>
                        <div class="mb-3" id="desktopPreviewBox">
                            @if ($HM::isVideo($desktopFile))
                                <video src="{{ $desktopUrl }}" class="w-100 border rounded" muted loop autoplay playsinline
                                    @if ($posterUrl) poster="{{ $posterUrl }}" @endif></video>
                            @else
                                <img src="{{ $desktopUrl }}" alt="" class="img-fluid border rounded">
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="hero_image">Replace banner</label>
                            <input type="file" class="form-control-file media-input" name="hero_image" id="hero_image"
                                data-preview="desktopPreviewBox" accept=".jpg,.jpeg,.png,.webp,.mp4,.webm,image/*,video/mp4,video/webm">
                            <small class="form-text text-muted">
                                <strong>Image:</strong> 1920 &times; 640 px (3:1), JPG / PNG / WebP, up to 5 MB.<br>
                                <strong>Video:</strong> MP4 (H.264) or WebM, same 3:1 shape, up to 50 MB – ideally under 10 MB and
                                10–20 seconds. It plays silently on a loop.
                                @if ($serverLimitMb)
                                    <br>This server currently accepts uploads up to <strong>{{ $serverLimitMb }} MB</strong>.
                                @endif
                            </small>
                        </div>

                        @if ($desktopFile)
                            <div class="custom-control custom-checkbox mb-3">
                                <input type="checkbox" class="custom-control-input" id="reset_hero_image"
                                    name="reset_hero_image" value="1">
                                <label class="custom-control-label" for="reset_hero_image">
                                    Remove uploaded banner and go back to the default image
                                </label>
                            </div>
                        @endif

                        <div class="form-group mb-0">
                            <label for="hero_poster">Video cover image <span class="text-muted">(for video banners)</span></label>
                            @if ($posterUrl)
                                <div class="mb-2"><img src="{{ $posterUrl }}" alt="" class="border rounded" style="max-width: 240px"></div>
                            @endif
                            <input type="file" class="form-control-file" name="hero_poster" id="hero_poster" accept=".jpg,.jpeg,.png,.webp">
                            <small class="form-text text-muted">
                                Shown while the video loads, and instead of the video for visitors who turn off animations.
                                Use a still from the video.
                            </small>
                            @if ($posterUrl)
                                <div class="custom-control custom-checkbox mt-1">
                                    <input type="checkbox" class="custom-control-input" id="remove_hero_poster" name="remove_hero_poster" value="1">
                                    <label class="custom-control-label" for="remove_hero_poster">Remove cover image</label>
                                </div>
                            @endif
                        </div>

                        <hr>

                        <div class="form-group">
                            <label for="hero_image_mobile">Mobile image or video <span class="text-muted">(optional)</span></label>
                            <div class="mb-2" id="mobilePreviewBox">
                                @if ($mobileUrl && $HM::isVideo($mobileFile))
                                    <video src="{{ $mobileUrl }}" class="border rounded" style="max-width: 220px" muted loop autoplay playsinline></video>
                                @elseif ($mobileUrl)
                                    <img src="{{ $mobileUrl }}" alt="" class="border rounded" style="max-width: 220px">
                                @endif
                            </div>
                            <input type="file" class="form-control-file media-input" name="hero_image_mobile"
                                id="hero_image_mobile" data-preview="mobilePreviewBox" data-max-width="220px"
                                accept=".jpg,.jpeg,.png,.webp,.mp4,.webm,image/*,video/mp4,video/webm">
                            <small class="form-text text-muted">
                                Shown on phones instead of the main banner. A taller shape (e.g. 1080 &times; 1080 px) keeps
                                the text readable. Image up to 5 MB, MP4 / WebM video up to 50 MB.
                                Leave empty to use the main banner everywhere.
                            </small>
                        </div>

                        @if ($setting->hero_image_mobile)
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="remove_hero_image_mobile"
                                    name="remove_hero_image_mobile" value="1">
                                <label class="custom-control-label" for="remove_hero_image_mobile">
                                    Remove mobile banner
                                </label>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Options</h3>
                    </div>
                    <div class="card-body">
                        <div class="custom-control custom-switch mb-3">
                            <input type="hidden" name="hero_enabled" value="0">
                            <input type="checkbox" class="custom-control-input" id="hero_enabled" name="hero_enabled"
                                value="1" {{ old('hero_enabled', $setting->hero_enabled ?? 1) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="hero_enabled">Show banner on homepage</label>
                        </div>

                        <div class="form-group">
                            <label for="hero_alt">Image description (alt text)</label>
                            <input type="text" class="form-control @error('hero_alt') is-invalid @enderror"
                                name="hero_alt" id="hero_alt" maxlength="255"
                                placeholder="e.g. India Law, AI & Tech Summit 2026 – New Delhi"
                                value="{{ old('hero_alt', $setting->hero_alt) }}">
                            <small class="form-text text-muted">Read by screen readers and search engines.</small>
                        </div>

                        <div class="form-group">
                            <label>When someone clicks the banner</label>
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input" id="click_register" name="hero_click"
                                    value="register" {{ $click === 'register' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="click_register">Open the registration form</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input" id="click_link" name="hero_click"
                                    value="link" {{ $click === 'link' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="click_link">Go to a link</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input" id="click_none" name="hero_click"
                                    value="none" {{ $click === 'none' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="click_none">Do nothing</label>
                            </div>
                        </div>

                        <div id="linkFields" class="{{ $click === 'link' ? '' : 'd-none' }}">
                            <div class="form-group">
                                <label for="hero_link">Link</label>
                                <input type="url" class="form-control @error('hero_link') is-invalid @enderror"
                                    name="hero_link" id="hero_link" placeholder="https://..."
                                    value="{{ old('hero_link', $setting->hero_link) }}">
                            </div>
                            <div class="custom-control custom-checkbox mb-3">
                                <input type="checkbox" class="custom-control-input" id="hero_new_tab"
                                    name="hero_new_tab" value="1"
                                    {{ old('hero_new_tab', $setting->hero_new_tab) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="hero_new_tab">Open in a new tab</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Save banner</button>
                        <a href="{{ route('home') }}" target="_blank" class="btn btn-outline-secondary btn-block">
                            View homepage
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop

@section('js')
    <script>
        (function() {
            document.querySelectorAll('.media-input').forEach(function(input) {
                input.addEventListener('change', function() {
                    var box = document.getElementById(this.dataset.preview);
                    var file = this.files && this.files[0];
                    if (!box || !file) return;
                    var isVideo = file.type.indexOf('video/') === 0 || /\.(mp4|webm)$/i.test(file.name);
                    var el = document.createElement(isVideo ? 'video' : 'img');
                    el.src = URL.createObjectURL(file);
                    el.className = 'border rounded' + (this.dataset.maxWidth ? '' : (isVideo ? ' w-100' : ' img-fluid'));
                    if (this.dataset.maxWidth) el.style.maxWidth = this.dataset.maxWidth;
                    if (isVideo) {
                        el.muted = true;
                        el.loop = true;
                        el.autoplay = true;
                        el.playsInline = true;
                    } else {
                        el.alt = '';
                    }
                    box.innerHTML = '';
                    box.appendChild(el);
                    if (isVideo) {
                        var p = el.play();
                        if (p && p.catch) p.catch(function() {});
                    }
                    if (file.size > 50 * 1024 * 1024 || (!isVideo && file.size > 5 * 1024 * 1024)) {
                        var warn = document.createElement('div');
                        warn.className = 'text-danger small mt-1';
                        warn.textContent = 'This file is too large (' + (file.size / 1048576).toFixed(1) + ' MB). Limit: ' +
                            (isVideo ? '50' : '5') + ' MB.';
                        box.appendChild(warn);
                    }
                });
            });
            document.querySelectorAll('input[name="hero_click"]').forEach(function(radio) {
                radio.addEventListener('change', function() {
                    document.getElementById('linkFields').classList.toggle('d-none', this.value !== 'link');
                });
            });
        })();
    </script>
@stop
