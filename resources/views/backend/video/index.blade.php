@extends('adminlte::page')

@section('title', 'Videos')

@section('content_header')
    <h1>Videos</h1>
@stop

@section('content')
    @include('backend.partials.alerts')

    <form method="post" action="{{ route('video.store') }}" class="card card-success card-outline">
        @csrf
        <div class="card-header"><h3 class="card-title">Add a video</h3></div>
        <div class="card-body">
            @if ($years->isEmpty())
                <div class="alert alert-warning mb-0">Add a year in <a href="{{ route('editions.index') }}">Years</a> first.</div>
            @else
                <div class="form-row align-items-end">
                    <div class="col-md-2 my-1">
                        <label class="small mb-0" for="v_year">Year <span class="text-danger">*</span></label>
                        <select name="edition_id" id="v_year" class="form-control" required>
                            @foreach ($years as $y)
                                <option value="{{ $y->id }}" @selected((string) old('edition_id', request('year', $years->first()->id)) === (string) $y->id)>{{ $y->year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 my-1">
                        <label class="small mb-0" for="v_title">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="v_title" class="form-control" maxlength="150" required value="{{ old('title') }}"
                            placeholder="e.g. Keynote – Future of Legal AI">
                    </div>
                    <div class="col-md-4 my-1">
                        <label class="small mb-0" for="v_link">YouTube link <span class="text-danger">*</span></label>
                        <input type="text" name="video" id="v_link" class="form-control" maxlength="250" required value="{{ old('video') }}"
                            placeholder="https://www.youtube.com/watch?v=… or https://youtu.be/…">
                    </div>
                    <div class="col-md-2 my-1">
                        <button type="submit" class="btn btn-success btn-block">+ Add video</button>
                    </div>
                </div>
            @endif
        </div>
    </form>

    @include('backend.partials.year-filter', [
        'action' => route('video.index'),
        'years' => $years,
        'searchPlaceholder' => 'Title or link…',
    ])

    @include('backend.partials.bulk-bar', [
        'formId' => 'bulkVideos', 'action' => route('video.bulk'), 'years' => $years, 'noun' => 'video',
    ])

    <div class="card">
        <div class="card-header py-2">
            <span class="text-muted small">{{ $videos->total() }} {{ \Illuminate\Support\Str::plural('video', $videos->total()) }}</span>
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover mb-0 year-table">
                <thead>
                    <tr>
                        <th style="width: 36px"><input type="checkbox" class="bulk-all" data-form="bulkVideos" aria-label="Select all on this page"></th>
                        <th style="width: 150px">Video</th>
                        <th>Title</th>
                        <th>Year</th>
                        <th style="width: 170px"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($videos as $video)
                        <tr>
                            <td><input type="checkbox" class="bulk-check" form="bulkVideos" name="ids[]" value="{{ $video->id }}" aria-label="Select {{ $video->title }}"></td>
                            <td>
                                @if ($video->thumbnailUrl())
                                    <a href="{{ $video->video }}" target="_blank" rel="noopener" title="Open on YouTube">
                                        <img src="{{ $video->thumbnailUrl() }}" alt="" loading="lazy" class="rounded" style="width: 128px; aspect-ratio: 16/9; object-fit: cover">
                                    </a>
                                @else
                                    <span class="badge badge-danger">Not a YouTube link</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('video.edit', $video) }}" class="font-weight-bold">{{ $video->title }}</a>
                                <div class="small text-muted text-truncate" style="max-width: 360px">{{ $video->video }}</div>
                            </td>
                            <td>
                                @if ($video->edition)
                                    <span class="badge badge-primary">{{ $video->edition->year }}</span>
                                @else
                                    <span class="badge badge-warning">no year</span>
                                @endif
                            </td>
                            <td class="text-nowrap text-right">
                                <a class="btn btn-info btn-sm" href="{{ route('video.edit', $video) }}"><i class="fas fa-pencil-alt"></i> Edit</a>
                                <form action="{{ route('video.destroy', $video) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Delete this video?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No videos match these filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($videos->hasPages())
            <div class="card-footer">{{ $videos->links('pagination::bootstrap-4') }}</div>
        @endif
    </div>
@stop

@section('css')
    <style>.year-table td { vertical-align: middle; }</style>
@stop

@section('js')
    @include('backend.partials.bulk-bar-js')
@stop
