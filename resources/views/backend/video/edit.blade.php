@extends('adminlte::page')

@section('title', 'Edit video')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Edit video</h1>
        <a href="{{ route('video.index') }}" class="btn btn-sm btn-outline-secondary">&larr; All videos</a>
    </div>
@stop

@section('content')
    @include('backend.partials.alerts')

    <div class="row">
        <div class="col-lg-7">
            <form method="post" action="{{ route('video.update', $video) }}" class="card card-primary card-outline">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label for="edition_id">Year <span class="text-danger">*</span></label>
                        <select name="edition_id" id="edition_id" class="form-control" required style="max-width: 200px">
                            <option value="">Choose year</option>
                            @foreach ($years as $y)
                                <option value="{{ $y->id }}" @selected((string) old('edition_id', $video->edition_id) === (string) $y->id)>{{ $y->year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="title">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control" maxlength="150" required value="{{ old('title', $video->title) }}">
                    </div>
                    <div class="form-group mb-0">
                        <label for="video">YouTube link <span class="text-danger">*</span></label>
                        <input type="text" name="video" id="video" class="form-control" maxlength="250" required value="{{ old('video', $video->video) }}">
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
        <div class="col-lg-5">
            @if ($video->embedUrl())
                <div class="card">
                    <div class="card-body p-2">
                        <div style="position: relative; padding-top: 56.25%">
                            <iframe src="{{ $video->embedUrl() }}" title="{{ $video->title }}" style="position: absolute; inset: 0; width: 100%; height: 100%; border: 0"
                                allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@stop
