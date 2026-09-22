@extends('adminlte::page')

@section('title', 'Marketing Popup')

@section('content_header')
    <h1>Marketing Popup</h1>
@stop

@section('content')
    @php
        $PB = \App\Support\PromoBanner::class;
        $file = $setting->promo_media;
        $mobileFile = $setting->promo_media_mobile;
        $fileUrl = $PB::url($file);
        $mobileUrl = $PB::url($mobileFile);
        $live = $PB::isLive($setting);
        // what this server accepts in one upload (php.ini)
        $toBytes = function ($v) {
            $v = trim((string) $v); $n = (float) $v; $u = strtolower(substr($v, -1));
            return (int) ($u === 'g' ? $n * 1073741824 : ($u === 'm' ? $n * 1048576 : ($u === 'k' ? $n * 1024 : $n)));
        };
        $serverLimit = min(array_filter([$toBytes(ini_get('upload_max_filesize')), $toBytes(ini_get('post_max_size'))]) ?: [0]);
        $serverLimitMb = $serverLimit ? round($serverLimit / 1048576) : null;
        $accept = '.jpg,.jpeg,.png,.webp,.gif,.mp4,.webm,image/*,video/mp4,video/webm';
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

    <div class="alert {{ $live ? 'alert-success' : 'alert-secondary' }}">
        <i class="fas fa-fw {{ $live ? 'fa-circle-check' : 'fa-circle-pause' }}"></i>
        <strong>{{ $live ? 'Showing now' : 'Not showing' }}</strong> &mdash; {{ $PB::statusNote($setting) }}
    </div>

    <form action="{{ route('promo.update') }}" method="post" enctype="multipart/form-data" id="promoForm">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card card-primary card-outline">
                    <div class="card-header"><h3 class="card-title">The popup</h3></div>
                    <div class="card-body">

                        <div class="custom-control custom-switch mb-4">
                            <input type="checkbox" class="custom-control-input" id="promo_enabled" name="promo_enabled"
                                value="1" {{ old('promo_enabled', $setting->promo_enabled) ? 'checked' : '' }}
                                {{ $file ? '' : 'disabled' }}>
                            <label class="custom-control-label" for="promo_enabled">
                                <strong>Show the popup on the home page</strong>
                            </label>
                            @unless ($file)
                                <small class="form-text text-muted">Upload a file first.</small>
                            @endunless
                        </div>

                        <div class="form-group">
                            <label for="promo_media">Popup file</label>
                            <div class="ob-drop" data-input="promo_media" data-preview="promoPreview">
                                <div class="ob-drop__preview" id="promoPreview">
                                    @if ($PB::isVideo($file))
                                        <video src="{{ $fileUrl }}" muted loop autoplay playsinline></video>
                                    @elseif ($fileUrl)
                                        <img src="{{ $fileUrl }}" alt="">
                                    @endif
                                </div>
                                <div class="ob-drop__hint">
                                    <i class="fas fa-cloud-arrow-up fa-lg mb-2 d-block"></i>
                                    <strong>Drag a file here</strong> or click to choose one
                                    <div class="ob-drop__name text-muted small mt-1">
                                        @if ($file)
                                            Current: {{ $file }}@if ($setting->promo_width) &middot; {{ $setting->promo_width }} &times; {{ $setting->promo_height }} px @endif
                                        @endif
                                    </div>
                                </div>
                                <input type="file" class="ob-drop__input" name="promo_media" id="promo_media" accept="{{ $accept }}">
                            </div>
                            <small class="form-text text-muted">
                                <strong>Image or GIF:</strong> JPG, PNG, WebP or GIF &ndash; up to 5 MB (GIF up to 15 MB).<br>
                                <strong>Short animation:</strong> MP4 (H.264) or WebM, up to 50 MB &ndash; it plays silently on a loop.<br>
                                For a sharp popup on retina screens, export at roughly <strong>1200 &times; 1400 px</strong>.
                                It is never stretched beyond its own size, so a small file will simply show small.
                                @if ($serverLimitMb)
                                    <br>This server currently accepts uploads up to <strong>{{ $serverLimitMb }} MB</strong>.
                                @endif
                            </small>
                            @if ($file)
                                <div class="custom-control custom-checkbox mt-2">
                                    <input type="checkbox" class="custom-control-input" id="remove_promo_media" name="remove_promo_media" value="1">
                                    <label class="custom-control-label" for="remove_promo_media">
                                        Remove this file (also switches the popup off)
                                    </label>
                                </div>
                            @endif
                        </div>

                        <hr>

                        <div class="form-group">
                            <label for="promo_media_mobile">Phone version <span class="text-muted">(optional)</span></label>
                            <div class="ob-drop ob-drop--narrow" data-input="promo_media_mobile" data-preview="promoPreviewMobile">
                                <div class="ob-drop__preview" id="promoPreviewMobile">
                                    @if ($PB::isVideo($mobileFile))
                                        <video src="{{ $mobileUrl }}" muted loop autoplay playsinline></video>
                                    @elseif ($mobileUrl)
                                        <img src="{{ $mobileUrl }}" alt="">
                                    @endif
                                </div>
                                <div class="ob-drop__hint">
                                    <i class="fas fa-mobile-screen fa-lg mb-2 d-block"></i>
                                    <strong>Drag a portrait file here</strong> or click to choose one
                                    <div class="ob-drop__name text-muted small mt-1">
                                        @if ($mobileFile) Current: {{ $mobileFile }} @endif
                                    </div>
                                </div>
                                <input type="file" class="ob-drop__input" name="promo_media_mobile" id="promo_media_mobile" accept="{{ $accept }}">
                            </div>
                            <small class="form-text text-muted">
                                Shown on phones instead of the main file. A tall shape (about 4:5, e.g. 1000 &times; 1250 px)
                                fills a phone screen properly. Leave empty to use the main file everywhere.
                            </small>
                            @if ($mobileFile)
                                <div class="custom-control custom-checkbox mt-2">
                                    <input type="checkbox" class="custom-control-input" id="remove_promo_media_mobile" name="remove_promo_media_mobile" value="1">
                                    <label class="custom-control-label" for="remove_promo_media_mobile">Remove the phone version</label>
                                </div>
                            @endif
                        </div>

                        <hr>

                        <div class="form-group">
                            <label for="promo_link">Destination link <span class="text-muted">(optional)</span></label>
                            <input type="url" class="form-control" name="promo_link" id="promo_link"
                                value="{{ old('promo_link', $setting->promo_link) }}" placeholder="https://www.oakbridge.events/ticket">
                            <small class="form-text text-muted">
                                Add a link and the whole popup becomes clickable. Leave it empty and the popup is
                                just a picture.
                            </small>
                        </div>

                        <div class="custom-control custom-checkbox mb-3">
                            <input type="checkbox" class="custom-control-input" id="promo_new_tab" name="promo_new_tab"
                                value="1" {{ old('promo_new_tab', $setting->promo_new_tab ?? 1) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="promo_new_tab">Open the link in a new tab</label>
                        </div>

                        <div class="form-group mb-0">
                            <label for="promo_alt">Description for screen readers <span class="text-muted">(optional)</span></label>
                            <input type="text" class="form-control" name="promo_alt" id="promo_alt" maxlength="255"
                                value="{{ old('promo_alt', $setting->promo_alt) }}"
                                placeholder="Early bird passes close on 30 October">
                            <small class="form-text text-muted">
                                Read out to visually impaired visitors, and shown if the file fails to load.
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card card-primary card-outline">
                    <div class="card-header"><h3 class="card-title">When it appears</h3></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="promo_delay">Delay after the page loads</label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="promo_delay" id="promo_delay"
                                    min="{{ $PB::MIN_DELAY }}" max="{{ $PB::MAX_DELAY }}"
                                    value="{{ old('promo_delay', $setting->promo_delay ?? 3) }}">
                                <div class="input-group-append"><span class="input-group-text">seconds</span></div>
                            </div>
                            <small class="form-text text-muted">3&ndash;5 seconds is usual. 0 shows it immediately.</small>
                        </div>

                        <div class="form-group">
                            <label for="promo_starts_at">Start date <span class="text-muted">(optional)</span></label>
                            <input type="date" class="form-control" name="promo_starts_at" id="promo_starts_at"
                                value="{{ old('promo_starts_at', $setting->promo_starts_at ? \Illuminate\Support\Carbon::parse($setting->promo_starts_at)->format('Y-m-d') : '') }}">
                        </div>

                        <div class="form-group">
                            <label for="promo_ends_at">End date <span class="text-muted">(optional)</span></label>
                            <input type="date" class="form-control" name="promo_ends_at" id="promo_ends_at"
                                value="{{ old('promo_ends_at', $setting->promo_ends_at ? \Illuminate\Support\Carbon::parse($setting->promo_ends_at)->format('Y-m-d') : '') }}">
                            <small class="form-text text-muted">
                                Leave both empty to run it until you switch it off. Dates are inclusive.
                            </small>
                        </div>

                        <hr>

                        <div class="custom-control custom-checkbox mb-3">
                            <input type="checkbox" class="custom-control-input" id="promo_dismissible" name="promo_dismissible"
                                value="1" {{ old('promo_dismissible', $setting->promo_dismissible ?? 1) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="promo_dismissible">
                                Show a &ldquo;Don't show this again&rdquo; tick box
                            </label>
                            <small class="form-text text-muted">
                                Without it, the popup comes back on the visitor's next visit.
                            </small>
                        </div>

                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="reset_dismissals" name="reset_dismissals" value="1">
                            <label class="custom-control-label" for="reset_dismissals">
                                Show it again to everyone
                            </label>
                            <small class="form-text text-muted">
                                Clears past dismissals. Uploading a new file does this automatically.
                            </small>
                        </div>
                    </div>
                </div>

                <div class="card card-outline card-secondary">
                    <div class="card-header"><h3 class="card-title">How visitors see it</h3></div>
                    <div class="card-body">
                        <ul class="pl-3 mb-3 small text-muted">
                            <li>Home page only.</li>
                            <li>Once per browser session &ndash; closing it keeps it closed for that visit.</li>
                            <li>Closes on the &times; button, the dark area around it, or the Esc key.</li>
                        </ul>
                        @if ($fileUrl)
                            <button type="button" class="btn btn-outline-secondary btn-block" id="promoPreviewBtn">
                                <i class="fas fa-fw fa-eye"></i> Preview it
                            </button>
                        @endif
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block mb-4">Save popup</button>
            </div>
        </div>
    </form>

    @if ($fileUrl)
        <div class="ob-preview" id="obPreview" hidden>
            <div class="ob-preview__backdrop" data-preview-close></div>
            <div class="ob-preview__panel">
                <button type="button" class="ob-preview__close" data-preview-close aria-label="Close">&times;</button>
                @if ($PB::isVideo($file))
                    <video src="{{ $fileUrl }}" muted loop autoplay playsinline></video>
                @else
                    <img src="{{ $fileUrl }}" alt="{{ $setting->promo_alt }}">
                @endif
            </div>
        </div>
    @endif
@stop

@section('css')
    <style>
        .ob-drop { position: relative; border: 2px dashed #ced4da; border-radius: .4rem; background: #fbfbfc;
            padding: 1.25rem; text-align: center; cursor: pointer; transition: border-color .15s, background .15s; }
        .ob-drop:hover { border-color: #9aa4ae; background: #f6f7f9; }
        .ob-drop.is-over { border-color: #007bff; background: #eaf3ff; }
        .ob-drop.is-invalid { border-color: #dc3545; background: #fdeef0; }
        .ob-drop__input { position: absolute; inset: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; }
        .ob-drop__preview:empty { display: none; }
        .ob-drop__preview { margin-bottom: .9rem; }
        .ob-drop__preview img, .ob-drop__preview video { max-width: 100%; max-height: 320px;
            border-radius: .3rem; border: 1px solid #e3e6ea; }
        .ob-drop--narrow .ob-drop__preview img, .ob-drop--narrow .ob-drop__preview video { max-height: 240px; }
        .ob-drop__hint { color: #6c757d; font-size: .9rem; pointer-events: none; }
        .ob-drop__error { color: #dc3545; font-size: .85rem; margin-top: .4rem; }

        .ob-preview { position: fixed; inset: 0; z-index: 3000; display: flex; align-items: center;
            justify-content: center; padding: 1.5rem; }
        .ob-preview[hidden] { display: none; }
        .ob-preview__backdrop { position: absolute; inset: 0; background: rgba(0, 0, 0, .68); }
        .ob-preview__panel { position: relative; max-width: min(92vw, 760px); }
        .ob-preview__panel img, .ob-preview__panel video { display: block; width: 100%; height: auto;
            max-height: 86vh; object-fit: contain; border-radius: .5rem; background: #fff;
            box-shadow: 0 18px 50px rgba(0, 0, 0, .45); }
        .ob-preview__close { position: absolute; top: -14px; right: -14px; width: 36px; height: 36px;
            border-radius: 50%; border: 0; background: #fff; font-size: 24px; line-height: 1; cursor: pointer; }
    </style>
@stop

@section('js')
    <script>
        (function () {
            var OK = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'mp4', 'webm'];
            var LIMITS = { gif: 15, mp4: 50, webm: 50 };   // megabytes; everything else is 5

            function extensionOf(name) {
                return (name.split('.').pop() || '').toLowerCase();
            }

            function complain(zone, message) {
                zone.classList.add('is-invalid');
                var box = zone.parentNode.querySelector('.ob-drop__error');
                if (!box) {
                    box = document.createElement('div');
                    box.className = 'ob-drop__error';
                    zone.parentNode.insertBefore(box, zone.nextSibling);
                }
                box.textContent = message;
            }

            function clearComplaint(zone) {
                zone.classList.remove('is-invalid');
                var box = zone.parentNode.querySelector('.ob-drop__error');
                if (box) box.remove();
            }

            function show(zone, file) {
                var extension = extensionOf(file.name);
                var limit = (LIMITS[extension] || 5) * 1048576;

                if (OK.indexOf(extension) === -1) {
                    complain(zone, 'That is a .' + extension + ' file. Use a JPG, PNG, WebP, GIF, MP4 or WebM.');
                    return false;
                }
                if (file.size > limit) {
                    complain(zone, file.name + ' is ' + (file.size / 1048576).toFixed(1) + ' MB. The limit for a .'
                        + extension + ' file is ' + (limit / 1048576) + ' MB.');
                    return false;
                }

                clearComplaint(zone);

                var preview = document.getElementById(zone.dataset.preview);
                var url = URL.createObjectURL(file);
                var node;

                if (extension === 'mp4' || extension === 'webm') {
                    node = document.createElement('video');
                    node.muted = true; node.loop = true; node.autoplay = true; node.playsInline = true;
                } else {
                    node = document.createElement('img');
                    node.alt = '';
                }
                node.src = url;
                node.addEventListener('load', function () { URL.revokeObjectURL(url); });

                preview.innerHTML = '';
                preview.appendChild(node);

                var name = zone.querySelector('.ob-drop__name');
                if (name) name.textContent = 'Selected: ' + file.name + ' (' + (file.size / 1048576).toFixed(1) + ' MB)';

                // choosing the main file means you want the popup on - dropped or browsed alike
                if (zone.dataset.input === 'promo_media') {
                    var toggle = document.getElementById('promo_enabled');
                    if (toggle) { toggle.disabled = false; toggle.checked = true; }
                }

                return true;
            }

            document.querySelectorAll('.ob-drop').forEach(function (zone) {
                var input = zone.querySelector('.ob-drop__input');

                input.addEventListener('change', function () {
                    if (input.files.length && !show(zone, input.files[0])) input.value = '';
                });

                ['dragenter', 'dragover'].forEach(function (type) {
                    zone.addEventListener(type, function (event) {
                        event.preventDefault();
                        zone.classList.add('is-over');
                    });
                });

                ['dragleave', 'dragend', 'drop'].forEach(function (type) {
                    zone.addEventListener(type, function () { zone.classList.remove('is-over'); });
                });

                zone.addEventListener('drop', function (event) {
                    event.preventDefault();
                    var dropped = event.dataTransfer && event.dataTransfer.files;
                    if (!dropped || !dropped.length) return;

                    if (!show(zone, dropped[0])) return;

                    // hand the dropped file to the real input so the form posts it
                    try {
                        var bag = new DataTransfer();
                        bag.items.add(dropped[0]);
                        input.files = bag.files;
                    } catch (e) {
                        complain(zone, 'This browser cannot accept dropped files. Click the box and choose the file instead.');
                    }
                });
            });

            // preview button
            var previewBtn = document.getElementById('promoPreviewBtn');
            var preview = document.getElementById('obPreview');
            if (previewBtn && preview) {
                previewBtn.addEventListener('click', function () { preview.hidden = false; });
                preview.querySelectorAll('[data-preview-close]').forEach(function (el) {
                    el.addEventListener('click', function () { preview.hidden = true; });
                });
                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') preview.hidden = true;
                });
            }
        })();
    </script>
@stop
