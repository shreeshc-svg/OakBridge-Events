@extends('adminlte::page')

@section('title', 'Edit year ' . $edition->year)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Year {{ $edition->year }}</h1>
        <a href="{{ route('editions.index') }}" class="btn btn-sm btn-outline-secondary">&larr; All years</a>
    </div>
@stop

@section('content')
    @include('backend.partials.alerts')

    <div class="row">
        <div class="col-lg-7">
            <form action="{{ route('editions.update', $edition) }}" method="post" enctype="multipart/form-data" class="card card-primary card-outline">
                @csrf
                @method('put')
                <div class="card-body">
                    <div class="form-group">
                        <label for="year">Year <span class="text-danger">*</span></label>
                        <input type="number" name="year" id="year" class="form-control" min="2000" max="2100" required
                            value="{{ old('year', $edition->year) }}" style="max-width: 160px">
                    </div>
                    <div class="form-group">
                        <label for="title">Name <span class="text-muted small">(optional, for your reference)</span></label>
                        <input type="text" name="title" id="title" class="form-control" maxlength="191" value="{{ old('title', $edition->title) }}">
                    </div>
                    <div class="form-group">
                        <label for="schedule_service_id">Schedule</label>
                        <select name="schedule_service_id" id="schedule_service_id" class="form-control">
                            <option value="">– none (no Schedule link for this year) –</option>
                            @foreach ($events as $event)
                                <option value="{{ $event->id }}" @selected((string) old('schedule_service_id', $edition->schedule_service_id) === (string) $event->id)>
                                    {{ $event->title }}{{ $event->date ? ' (' . $event->date->format('M Y') . ')' : '' }}{{ $event->published === '1' ? '' : ' – unpublished' }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Schedule › {{ $edition->year }} in the header opens this event's page. Only published events are linked.</small>
                    </div>
                    <div class="form-group">
                        <label for="speakers_banner">Speakers page banner</label>
                        {{-- an uploaded banner is shown whole with the title below it; the standard one has the title on top --}}
                        <div class="banner-preview mb-2" id="bannerPreview"
                            data-default="{{ asset('public/assets/images/background/bread2.webp') }}"
                            data-has-custom="{{ $edition->speakers_banner ? 1 : 0 }}">
                            <img src="{{ $edition->speakers_banner ? \App\Support\Uploads::url($edition->speakers_banner) : asset('public/assets/images/background/bread2.webp') }}" alt="">
                            <span class="banner-preview__over">Speakers {{ $edition->year }}</span>
                            <span class="banner-preview__under">Speakers {{ $edition->year }}</span>
                        </div>
                        <div class="alert alert-warning py-1 px-2 small mb-2 d-none" id="bannerShapeNote"></div>
                        <input type="file" name="speakers_banner" id="speakers_banner" class="form-control-file" accept=".jpg,.jpeg,.png,.webp">
                        <small class="form-text text-muted">
                            Upload finished artwork (with its own text if you like) for the top of
                            <a href="{{ route('speakers', $edition->year) }}" target="_blank" rel="noopener">/speakers/{{ $edition->year }}</a>.
                            It is the same size as the standard banner – full width, 350 px tall – with nothing on top of it;
                            “Speakers {{ $edition->year }}” goes underneath. Make it <strong>1920 × 470 px</strong> and keep all text inside the
                            <strong>left 1000 px, between 60 and 410 px from the top</strong> – that part shows on every screen; on phones and tablets
                            the right-hand side is trimmed, so put only decoration there. An image of another shape is trimmed top and bottom
                            (the preview shows what is kept). JPG / PNG / WebP up to 4 MB.
                            @unless ($edition->speakers_banner) Until you upload one, the standard banner is used, with the title on it. @endunless
                        </small>
                        @if ($edition->speakers_banner)
                            <div class="custom-control custom-checkbox mt-1">
                                <input type="checkbox" class="custom-control-input" id="remove_speakers_banner" name="remove_speakers_banner" value="1">
                                <label class="custom-control-label" for="remove_speakers_banner">Remove this banner (go back to the standard one)</label>
                            </div>
                        @endif
                    </div>
                    <div class="custom-control custom-switch">
                        <input type="hidden" name="is_visible" value="0">
                        <input type="checkbox" class="custom-control-input" id="is_visible" name="is_visible" value="1" @checked(old('is_visible', $edition->is_visible))>
                        <label class="custom-control-label" for="is_visible">Show this year on the website</label>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header"><h3 class="card-title">In {{ $edition->year }}</h3></div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <a href="{{ route('team.index', ['year' => $edition->id, 'role' => 'Speaker']) }}">Speakers</a>
                        <span class="badge badge-pill badge-primary">{{ $edition->speakers_count }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <a href="{{ route('sponsors.index', ['year' => $edition->id]) }}">Sponsors &amp; exhibitors</a>
                        <span class="badge badge-pill badge-primary">{{ $edition->sponsors_count }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <a href="{{ route('gallery.index', ['year' => $edition->id]) }}">Gallery images</a>
                        <span class="badge badge-pill badge-primary">{{ $edition->galleries_count }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <a href="{{ route('video.index', ['year' => $edition->id]) }}">Videos</a>
                        <span class="badge badge-pill badge-primary">{{ $edition->videos_count }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .banner-preview { position: relative; border-radius: 6px; overflow: hidden; background: #0b1d3a; }
        .banner-preview img { display: block; width: 100%; aspect-ratio: 1440 / 350; object-fit: cover; object-position: left center; }
        .banner-preview__over, .banner-preview__under { display: none; font-weight: 700; }
        .banner-preview__over { position: absolute; inset: 0; align-items: center; justify-content: center; color: #fff; font-size: 26px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, .45); background: rgba(0, 0, 0, .2); }
        .banner-preview__under { padding: 8px 0; background: #fff; color: #212639; text-align: center; font-size: 18px; border: 1px solid #e5e7eb; border-top: 0; border-radius: 0 0 6px 6px; }
        .banner-preview.is-standard .banner-preview__over { display: flex; }
        .banner-preview.is-custom .banner-preview__under { display: block; }
    </style>
@stop

@section('js')
    <script>
        // preview: an uploaded banner shows whole with the title underneath; the standard one has the title on top
        (function () {
            var input = document.getElementById('speakers_banner');
            var preview = document.getElementById('bannerPreview');
            var img = preview.querySelector('img');
            var remove = document.getElementById('remove_speakers_banner');
            var note = document.getElementById('bannerShapeNote');
            function mode(custom) { preview.classList.toggle('is-custom', custom); preview.classList.toggle('is-standard', !custom); checkShape(); }
            // warn when the image is a different shape from the banner (about 4.1 : 1), since the top and bottom get trimmed
            function checkShape() {
                note.classList.add('d-none');
                if (!preview.classList.contains('is-custom') || !img.naturalWidth) { return; }
                var ratio = img.naturalWidth / img.naturalHeight;
                if (ratio < 3.4) {
                    var kept = Math.round(100 * ratio / 4.11);
                    note.textContent = 'This image is ' + img.naturalWidth + ' × ' + img.naturalHeight + ' px, so only the middle ' + kept +
                        '% of its height shows (as previewed). For the whole image, use 1920 × 470 px.';
                    note.classList.remove('d-none');
                }
            }
            img.addEventListener('load', checkShape);
            mode(preview.dataset.hasCustom === '1');
            input.addEventListener('change', function () {
                if (input.files && input.files[0]) {
                    img.src = URL.createObjectURL(input.files[0]);
                    mode(true);
                    if (remove) { remove.checked = false; }
                }
            });
            if (remove) {
                remove.addEventListener('change', function () {
                    if (remove.checked) { img.src = preview.dataset.default; input.value = ''; mode(false); }
                });
            }
        })();
    </script>
@stop
