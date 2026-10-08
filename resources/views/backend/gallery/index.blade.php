@extends('adminlte::page')

@section('title', 'Gallery')

@section('content_header')
    <h1>Gallery images</h1>
@stop

@section('content')
    @include('backend.partials.alerts')

    {{-- upload --}}
    <form action="{{ route('gallery.store') }}" method="post" enctype="multipart/form-data" class="card card-success card-outline" id="uploadForm">
        @csrf
        <div class="card-header"><h3 class="card-title">Upload images</h3></div>
        <div class="card-body">
            @if ($years->isEmpty())
                <div class="alert alert-warning mb-0">Add a year in <a href="{{ route('editions.index') }}">Years</a> before uploading.</div>
            @else
                <div class="form-row align-items-start">
                    <div class="col-md-2 my-1">
                        <label class="small mb-0" for="up_year">Year <span class="text-danger">*</span></label>
                        <select name="edition_id" id="up_year" class="form-control" required>
                            @foreach ($years as $y)
                                <option value="{{ $y->id }}" @selected((string) old('edition_id', request('year', $years->first()->id)) === (string) $y->id)>{{ $y->year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-8 my-1">
                        <label class="small mb-0">Images <span class="text-muted">(up to 50 at a time, 5 MB each – JPG, PNG, WebP, GIF)</span></label>
                        <label class="drop-zone mb-0" id="dropZone" for="up_files">
                            <input type="file" name="src[]" id="up_files" accept=".jpg,.jpeg,.png,.webp,.gif" multiple required>
                            <span class="dz-text"><i class="fas fa-cloud-upload-alt"></i> Drag images here or <u>browse</u></span>
                            <span class="dz-count small text-primary"></span>
                        </label>
                    </div>
                    <div class="col-md-2 my-1">
                        <label class="small mb-0 d-none d-md-block">&nbsp;</label>
                        <button type="submit" class="btn btn-success btn-block">Upload</button>
                    </div>
                </div>
            @endif
        </div>
    </form>

    @include('backend.partials.year-filter', [
        'action' => route('gallery.index'),
        'years' => $years,
        'searchPlaceholder' => 'File name…',
    ])

    @include('backend.partials.bulk-bar', [
        'formId' => 'bulkGallery', 'action' => route('gallery.bulk'), 'years' => $years, 'noun' => 'image',
    ])

    <div class="card">
        <div class="card-header py-2 d-flex align-items-center">
            <label class="mb-0 mr-3 small"><input type="checkbox" class="bulk-all mr-1" data-form="bulkGallery"> Select all on this page</label>
            <span class="text-muted small ml-auto">
                {{ $images->total() }} {{ \Illuminate\Support\Str::plural('image', $images->total()) }}
                @if ($images->total() > $images->count()) – showing {{ $images->firstItem() }}–{{ $images->lastItem() }} @endif
            </span>
        </div>
        <div class="card-body">
            <div class="gallery-grid">
                @forelse ($images as $image)
                    <div class="g-tile">
                        <input type="checkbox" class="bulk-check g-check" form="bulkGallery" name="ids[]" value="{{ $image->id }}" aria-label="Select image {{ $image->id }}">
                        <a href="{{ asset('public/uploads/images/our-gallery/' . $image->name) }}" target="_blank" rel="noopener" class="g-img">
                            <img src="{{ asset('public/uploads/images/our-gallery/' . $image->name) }}" alt="" loading="lazy">
                        </a>
                        <div class="g-meta">
                            @if ($image->edition)
                                <span class="badge badge-primary">{{ $image->edition->year }}</span>
                            @else
                                <span class="badge badge-warning">no year</span>
                            @endif
                            <span class="g-name" title="{{ $image->name }}">{{ $image->name }}</span>
                        </div>
                        <div class="g-actions">
                            <button type="button" class="btn btn-xs btn-outline-primary edit-image"
                                data-action="{{ route('gallery.update', $image) }}" data-edition="{{ $image->edition_id }}"
                                data-src="{{ asset('public/uploads/images/our-gallery/' . $image->name) }}">Edit</button>
                            <form action="{{ route('gallery.destroy', $image) }}" method="post" class="d-inline" onsubmit="return confirm('Delete this image?');">
                                @csrf
                                @method('delete')
                                <button type="submit" class="btn btn-xs btn-outline-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">No images match these filters.</p>
                @endforelse
            </div>
        </div>
        @if ($images->hasPages())
            <div class="card-footer">{{ $images->links('pagination::bootstrap-4') }}</div>
        @endif
    </div>

    {{-- edit one image --}}
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="post" enctype="multipart/form-data" id="imageForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Edit image</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3"><img id="imagePreview" src="" alt="" style="max-height: 220px; max-width: 100%"></div>
                    <div class="form-group">
                        <label for="imageEdition">Year</label>
                        <select name="edition_id" id="imageEdition" class="form-control" required>
                            @foreach ($years as $y)
                                <option value="{{ $y->id }}">{{ $y->year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label>Replace with a new image <span class="text-muted small">(optional)</span></label>
                        <input type="file" name="image" class="form-control-file" accept=".jpg,.jpeg,.png,.webp,.gif">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    <style>
        .drop-zone { position: relative; display: flex; flex-direction: column; align-items: center; justify-content: center;
            min-height: 78px; border: 2px dashed #ced4da; border-radius: 6px; background: #fafbfc; cursor: pointer; text-align: center; }
        .drop-zone.is-over { border-color: #28a745; background: #f0fff4; }
        .drop-zone input[type=file] { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
        .drop-zone .dz-text { color: #6c757d; }
        .gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); gap: 14px; }
        .g-tile { position: relative; border: 1px solid #dee2e6; border-radius: 6px; background: #fff; padding: 6px; }
        .g-tile:has(.g-check:checked) { border-color: #007bff; box-shadow: 0 0 0 2px rgba(0,123,255,.25); }
        .g-check { position: absolute; top: 10px; left: 10px; z-index: 2; width: 18px; height: 18px; }
        .g-img { display: block; aspect-ratio: 4 / 3; overflow: hidden; border-radius: 4px; background: #f4f4f4; }
        .g-img img { width: 100%; height: 100%; object-fit: cover; }
        .g-meta { display: flex; align-items: center; gap: 6px; margin: 6px 0 4px; font-size: 11px; }
        .g-name { color: #6c757d; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .g-actions { display: flex; gap: 4px; }
    </style>
@stop

@section('js')
    @include('backend.partials.bulk-bar-js')
    <script>
        (function () {
            var zone = document.getElementById('dropZone');
            if (zone) {
                var input = document.getElementById('up_files');
                var count = zone.querySelector('.dz-count');
                ['dragenter', 'dragover'].forEach(function (t) { zone.addEventListener(t, function () { zone.classList.add('is-over'); }); });
                ['dragleave', 'drop'].forEach(function (t) { zone.addEventListener(t, function () { zone.classList.remove('is-over'); }); });
                input.addEventListener('change', function () {
                    var n = input.files.length;
                    count.textContent = n ? n + ' image' + (n === 1 ? '' : 's') + ' ready to upload' : '';
                });
            }
            document.querySelectorAll('.edit-image').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    document.getElementById('imageForm').action = btn.dataset.action;
                    document.getElementById('imagePreview').src = btn.dataset.src;
                    document.getElementById('imageEdition').value = btn.dataset.edition || '';
                    $('#imageModal').modal('show');
                });
            });
        })();
    </script>
@stop
