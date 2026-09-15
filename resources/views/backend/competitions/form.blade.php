@extends('adminlte::page')

@php $isNew = ! $competition->exists; @endphp

@section('title', $isNew ? 'Add competition' : 'Edit competition')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1>{{ $isNew ? 'Add competition' : 'Edit competition' }}</h1>
        <a href="{{ route('competitions.index') }}" class="btn btn-outline-secondary btn-sm">&larr; All competitions</a>
    </div>
@stop

@section('content')
    @include('backend.partials.alerts')

    <form action="{{ $isNew ? route('competitions.store') : route('competitions.update', $competition) }}" method="post"
        enctype="multipart/form-data">
        @csrf
        @unless ($isNew)
            @method('put')
        @endunless

        <div class="row">
            <div class="col-lg-8">
                <div class="card card-primary card-outline">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title"
                                value="{{ old('title', $competition->title) }}" required maxlength="200">
                        </div>
                        <div class="form-group">
                            <label for="subtitle">Subtitle</label>
                            <input type="text" class="form-control" id="subtitle" name="subtitle"
                                value="{{ old('subtitle', $competition->subtitle) }}" maxlength="200">
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control rich-editor" id="description" name="description" rows="12">{{ old('description', $competition->description) }}</textarea>
                            <small class="form-text text-muted">Bulleted lists are shown with the site's tick bullets.</small>
                        </div>
                    </div>
                </div>

                <div class="card card-primary card-outline">
                    <div class="card-header"><h3 class="card-title">Buttons</h3></div>
                    <div class="card-body">
                        @php $buttons = old('buttons', $competition->buttons ?? []); @endphp
                        @for ($b = 0; $b < 3; $b++)
                            @php $button = $buttons[$b] ?? []; @endphp
                            <div class="form-row align-items-end {{ $b ? 'border-top pt-2' : '' }}">
                                <div class="col-md-3 form-group">
                                    <label class="small mb-0">Button {{ $b + 1 }} text</label>
                                    <input type="text" class="form-control form-control-sm" name="buttons[{{ $b }}][label]"
                                        value="{{ $button['label'] ?? '' }}" maxlength="60" placeholder="e.g. Learn More">
                                </div>
                                <div class="col-md-5 form-group">
                                    <label class="small mb-0">Link</label>
                                    <input type="text" class="form-control form-control-sm" name="buttons[{{ $b }}][url]"
                                        value="{{ $button['url'] ?? '' }}" maxlength="500" placeholder="https://... or /public/...pdf">
                                </div>
                                <div class="col-md-2 form-group">
                                    <label class="small mb-0">Colour</label>
                                    <select class="form-control form-control-sm" name="buttons[{{ $b }}][style]">
                                        @foreach (\App\Http\Controllers\CompetitionController::BUTTON_STYLES as $style => $label)
                                            <option value="{{ $style }}" @selected(($button['style'] ?? 'one') === $style)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="btn_tab_{{ $b }}"
                                            name="buttons[{{ $b }}][new_tab]" value="1" @checked(!empty($button['new_tab']) || empty($button))>
                                        <label class="custom-control-label small" for="btn_tab_{{ $b }}">New tab</label>
                                    </div>
                                </div>
                            </div>
                        @endfor
                        <small class="text-muted">Leave a button's text empty to hide it. To link a PDF, upload it to the server and paste its path.</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card card-primary card-outline">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Image</label>
                            @if ($competition->image)
                                <div class="mb-2">
                                    <img src="{{ \App\Support\Uploads::url($competition->image) }}" alt="" class="img-fluid img-thumbnail">
                                </div>
                            @endif
                            <input type="file" class="form-control-file" name="image" accept=".jpg,.jpeg,.png,.webp,.gif">
                        </div>
                        <div class="form-group">
                            <label for="sort_order">Order</label>
                            <input type="number" class="form-control" id="sort_order" name="sort_order" min="0" max="9999"
                                value="{{ old('sort_order', $competition->sort_order) }}">
                            <small class="form-text text-muted">Lower numbers show first.</small>
                        </div>
                        <div class="custom-control custom-switch mb-3">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                                @checked(old('is_active', $competition->is_active))>
                            <label class="custom-control-label" for="is_active">Show on the website</label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop

@section('js')
    @include('backend.partials.rich-editor')
@stop
