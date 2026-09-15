@extends('adminlte::page')

@section('title', 'SEO')

@section('content_header')
    <h1>SEO</h1>
@stop

@section('content')
    @include('backend.partials.alerts')

    <p class="text-muted">
        Title and description shown in Google results and when a page is shared. Leave a box empty to keep the page's
        built-in text (shown in grey). Each event's schedule page has its own SEO box in <a href="{{ route('service.index') }}">Events</a>.
    </p>

    <form action="{{ route('seo.update') }}" method="post">
        @csrf
        @foreach ($pages as $page => $info)
            @php
                $row = $saved[$page] ?? null;
                $url = Route::has($page) ? route($page) : null;
            @endphp
            <div class="card card-outline {{ $row ? 'card-info' : 'card-secondary' }} mb-3">
                <div class="card-header py-2">
                    <strong>{{ $info[0] }}</strong>
                    @if ($url)
                        <a href="{{ $url }}" target="_blank" class="small ml-2">{{ parse_url($url, PHP_URL_PATH) ?: '/' }}</a>
                    @endif
                    @if ($row)
                        <span class="badge badge-info ml-2">Custom</span>
                    @endif
                </div>
                <div class="card-body py-2">
                    <div class="form-row">
                        <div class="col-lg-5 form-group mb-2">
                            <label class="small mb-0">Title <span class="text-muted seo-count" data-for="t_{{ $loop->index }}"></span></label>
                            <input type="text" class="form-control form-control-sm seo-input" id="t_{{ $loop->index }}" data-limit="60"
                                name="seo[{{ $page }}][title]" maxlength="255"
                                value="{{ old("seo.$page.title", $row?->title) }}"
                                placeholder="{{ \App\Support\Seo::builtIn($page, 'title') }}">
                        </div>
                        <div class="col-lg-7 form-group mb-2">
                            <label class="small mb-0">Keywords</label>
                            <input type="text" class="form-control form-control-sm" name="seo[{{ $page }}][keywords]" maxlength="1000"
                                value="{{ old("seo.$page.keywords", $row?->keywords) }}"
                                placeholder="{{ \App\Support\Seo::builtIn($page, 'keywords') }}">
                        </div>
                        <div class="col-12 form-group mb-2">
                            <label class="small mb-0">Description <span class="text-muted seo-count" data-for="d_{{ $loop->index }}"></span></label>
                            <textarea class="form-control form-control-sm seo-input" id="d_{{ $loop->index }}" data-limit="160" rows="2"
                                name="seo[{{ $page }}][description]" maxlength="1000"
                                placeholder="{{ \App\Support\Seo::builtIn($page, 'description') }}">{{ old("seo.$page.description", $row?->description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="mb-4 sticky-bottom">
            <button type="submit" class="btn btn-primary">Save SEO settings</button>
        </div>
    </form>
@stop

@section('js')
    <script>
        document.querySelectorAll('.seo-input').forEach(function(input) {
            var counter = document.querySelector('.seo-count[data-for="' + input.id + '"]');
            var limit = parseInt(input.dataset.limit, 10);
            function update() {
                var n = input.value.length;
                counter.textContent = n ? '(' + n + '/' + limit + ' recommended)' : '';
                counter.className = 'seo-count ' + (n > limit ? 'text-danger' : 'text-muted');
            }
            input.addEventListener('input', update);
            update();
        });
    </script>
@stop
