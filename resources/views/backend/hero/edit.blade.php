@extends('adminlte::page')

@section('title', 'Hero Banner')

@section('content_header')
    <h1>Hero Banner</h1>
@stop

@section('content')
    @php
        $heroDir = 'public/uploads/images/hero/';
        $desktopUrl = $setting->hero_image
            ? asset($heroDir . $setting->hero_image)
            : asset(\App\Http\Controllers\HeroBannerController::DEFAULT_IMAGE);
        $mobileUrl = $setting->hero_image_mobile ? asset($heroDir . $setting->hero_image_mobile) : null;
        $click = old('hero_click', $setting->hero_click ?? 'register');
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
                        <h3 class="card-title">Banner image</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-2">
                            Currently showing{{ $setting->hero_image ? '' : ' (default banner)' }}:
                        </p>
                        <img src="{{ $desktopUrl }}" alt="" class="img-fluid border rounded mb-3" id="desktopPreview">

                        <div class="form-group">
                            <label for="hero_image">Replace banner image</label>
                            <input type="file" class="form-control-file" name="hero_image" id="hero_image"
                                accept=".jpg,.jpeg,.png,.webp">
                            <small class="form-text text-muted">
                                Recommended 1920 &times; 640 px (3:1), JPG / PNG / WebP, up to 5 MB.
                                WebP keeps the page fastest.
                            </small>
                        </div>

                        @if ($setting->hero_image)
                            <div class="custom-control custom-checkbox mb-3">
                                <input type="checkbox" class="custom-control-input" id="reset_hero_image"
                                    name="reset_hero_image" value="1">
                                <label class="custom-control-label" for="reset_hero_image">
                                    Remove uploaded image and go back to the default banner
                                </label>
                            </div>
                        @endif

                        <hr>

                        <div class="form-group">
                            <label for="hero_image_mobile">Mobile image <span class="text-muted">(optional)</span></label>
                            @if ($mobileUrl)
                                <div class="mb-2">
                                    <img src="{{ $mobileUrl }}" alt="" class="border rounded" style="max-width: 220px"
                                        id="mobilePreview">
                                </div>
                            @else
                                <img src="" alt="" class="border rounded mb-2 d-none" style="max-width: 220px"
                                    id="mobilePreview">
                            @endif
                            <input type="file" class="form-control-file" name="hero_image_mobile"
                                id="hero_image_mobile" accept=".jpg,.jpeg,.png,.webp">
                            <small class="form-text text-muted">
                                Shown on phones instead of the main image. A taller image (e.g. 1080 &times; 1080 px)
                                keeps the text readable. Leave empty to use the main image everywhere.
                            </small>
                        </div>

                        @if ($setting->hero_image_mobile)
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="remove_hero_image_mobile"
                                    name="remove_hero_image_mobile" value="1">
                                <label class="custom-control-label" for="remove_hero_image_mobile">
                                    Remove mobile image
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
            function preview(input, img) {
                if (input.files && input.files[0]) {
                    img.src = URL.createObjectURL(input.files[0]);
                    img.classList.remove('d-none');
                }
            }
            document.getElementById('hero_image').addEventListener('change', function() {
                preview(this, document.getElementById('desktopPreview'));
            });
            document.getElementById('hero_image_mobile').addEventListener('change', function() {
                preview(this, document.getElementById('mobilePreview'));
            });
            document.querySelectorAll('input[name="hero_click"]').forEach(function(radio) {
                radio.addEventListener('change', function() {
                    document.getElementById('linkFields').classList.toggle('d-none', this.value !== 'link');
                });
            });
        })();
    </script>
@stop
