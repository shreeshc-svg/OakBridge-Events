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
                        <div class="banner-preview mb-2" id="bannerPreview"
                            data-default="{{ asset('public/assets/images/background/bread2.webp') }}"
                            style="background-image: url({{ $edition->speakers_banner ? \App\Support\Uploads::url($edition->speakers_banner) : asset('public/assets/images/background/bread2.webp') }});">
                            <span>Speakers {{ $edition->year }}</span>
                        </div>
                        <input type="file" name="speakers_banner" id="speakers_banner" class="form-control-file" accept=".jpg,.jpeg,.png,.webp">
                        <small class="form-text text-muted">
                            Shown behind “Speakers {{ $edition->year }}” at the top of
                            <a href="{{ route('speakers', $edition->year) }}" target="_blank" rel="noopener">/speakers/{{ $edition->year }}</a>.
                            Wide image, about 1920 × 500 px, JPG / PNG / WebP up to 4 MB. Keep the middle clear – the title sits there and phones crop the sides.
                            @unless ($edition->speakers_banner) Until you upload one, the standard banner is used. @endunless
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
        .banner-preview { position: relative; height: 140px; border-radius: 6px; background-size: cover; background-position: center;
            display: flex; align-items: center; justify-content: center; }
        .banner-preview::before { content: ""; position: absolute; inset: 0; background: rgba(0, 0, 0, .25); border-radius: 6px; }
        .banner-preview span { position: relative; color: #fff; font-size: 26px; font-weight: 700; text-shadow: 0 2px 8px rgba(0, 0, 0, .4); }
    </style>
@stop

@section('js')
    <script>
        // preview a newly chosen banner before saving
        (function () {
            var input = document.getElementById('speakers_banner');
            var preview = document.getElementById('bannerPreview');
            var remove = document.getElementById('remove_speakers_banner');
            input.addEventListener('change', function () {
                if (input.files && input.files[0]) {
                    preview.style.backgroundImage = 'url(' + URL.createObjectURL(input.files[0]) + ')';
                    if (remove) { remove.checked = false; }
                }
            });
            if (remove) {
                remove.addEventListener('change', function () {
                    if (remove.checked) { preview.style.backgroundImage = 'url(' + preview.dataset.default + ')'; input.value = ''; }
                });
            }
        })();
    </script>
@stop
