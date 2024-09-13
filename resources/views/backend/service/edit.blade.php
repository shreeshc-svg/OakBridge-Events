@extends('adminlte::page')

@section('title', 'Edit Product')

@section('content_header')
    <div class="d-flex justify-content-between px-md-3 align-items-center">
        <div class="pt-3 px-2">
            <a href="{{ route('service.index') }}" class="btn btn-primary">Back</a>
        </div>
    </div>
@stop
@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <link href="{{ asset('ckeditor/plugins/codesnippet/lib/highlight/styles/default.css') }}" rel="stylesheet">
@stop

@section('content')

    <div class="container-fluid mt-n3">
        @if (session()->has('success'))
            <div class="alert alert-dismissable alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>
                    {!! session()->get('success') !!}
                </strong>
            </div>
        @endif
        @if (count($errors) > 0)
            <div class="alert alert-dismissable alert-danger mt-3">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>Whoops!</strong> There were some problems with your input.<br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('service.update', $service->id) }}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            @method('PUT')
            <div class="row px-3 py-3">
                <div class="col-md-8">
                    <div class="card card-light">
                        <div class="card-header">
                            <h3 class="card-title">Edit Job
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus" aria-hidden="true">
                                    </i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="inputStatus">Title
                                </label>
                                <input class="form-control @error('title') is-invalid @enderror" type="text"
                                    id="title" name="title" placeholder="Title here.." value="{{ $service->title }}">
                                @error('title')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="inputStatus">Slug
                                </label>
                                <small>&nbsp;&nbsp;Unique url of the Service
                                </small>
                                <input class="form-control bg-light @error('slug') is-invalid @enderror" type="text"
                                    id="slug" name="slug" placeholder="slug here.." value="{{ $service->slug }}">
                                @error('slug')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="">Service Description
                                </label>
                                <textarea style="height: 600px;" id="editor" name="body" value="{{ $service->body }}" name="body" required />{{ $service->body }}</textarea>
                                @error('body')
                                    <li class="text-danger">{{ $message }}</li>
                                @enderror
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    {{-- <div class="card card-light">
                        <div class="card-header">
                            <h3 class="card-title">Video Link
                            </h3>
                            <small>&nbsp; &nbsp;Youtube video id only, Ex: <span
                                    class="text-danger text-underline">LMmuChXra_M</span></small>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus" aria-hidden="true">
                                    </i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <input class="form-control" type="text" name="video" placeholder="Youtube video link"
                                    value="{{ $service->video }}">
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div> --}}
                    <div class="card card-light">
                        <div class="card-header">
                            <h3 class="card-title">Excerpt
                            </h3>
                            <small>&nbsp;&nbsp;Small Description of the Service
                            </small>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus" aria-hidden="true">
                                    </i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <textarea class="form-control" name="excerpt" id="" value="{{ $service->excerpt }}" cols="30"
                                    rows="5">{{ $service->excerpt }}</textarea>
                            </div>
                        </div>

                    </div>
                    <!-- /.card -->
                    {{-- seo --}}
                    <div class="card card-light">
                        <div class="card-header">
                            <h3 class="card-title">SEO
                            </h3>
                            <small>&nbsp;&nbsp;Search engine details
                            </small>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus" aria-hidden="true">
                                    </i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body pt-3 pb-0">
                            <div class="form-group">
                                <label for="">SEO Title
                                </label>
                                <input placeholder="Service title here for seo..." type="text" class="form-control"
                                    name="meta_title" id="" value="{{ $service->meta_title }}">
                            </div>
                        </div>
                        <div class="card-body  pt-0 pb-0">
                            <div class="form-group">
                                <label for="">SEO Description
                                </label>
                                <textarea placeholder="Service description here for seo..." class="form-control" name="meta_description"
                                    id="" cols="0" rows="4" value="{{ $service->meta_description }}">{{ $service->meta_description }}</textarea>
                            </div>
                        </div>
                        <div class="card-body pt-0 pb-0">
                            <div class="form-group">
                                <label for="">SEO Keywords
                                </label>
                                <input type="text" class="form-control" placeholder="keyword1, keyword2, keyword3"
                                    name="meta_keyword" id="" value="{{ $service->meta_keyword }}">
                            </div>
                        </div>
                        {{-- <div class="card-body pt-0">
                            <div class="form-group select2-primary">
                                <label for="">SEO Tags
                                </label>
                                <select id="tags" name="tag[]" class="select2" multiple="multiple"
                                    data-placeholder="Search Tags" style="width: 100%;">
                                    @foreach ($tags as $tag)
                                        <option value="{{ $tag->id }}"
                                            {{ $service->tags->contains('id', $tag->id) ? 'selected' : '' }}>
                                            {{ $tag->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div> --}}
                        <!-- /.card-body -->
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="sticky-top">
                        <div class="card card-info sticky-bottom">
                            <div class="card-header">
                                <h3 class="card-title">Service Details
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                        title="Collapse">
                                        <i class="fas fa-minus" aria-hidden="true">
                                        </i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body pb-0">
                                <div class="form-group select2-dark">
                                    <label>Category
                                    </label>
                                    <small>&nbsp;&nbsp;</small>



                                    <select id="category" name="scategory[]" class="select2" multiple="multiple"
                                        data-placeholder="Search Category" style="width: 100%;" required>
                                        <option value="">None</option>
                                        @if ($scategories)
                                            @foreach ($scategories as $item)
                                                <?php $dash = ''; ?>
                                                <option value="{{ $item->id }}"
                                                    @if (in_array($item->id, $service->scategories->pluck('id')->toArray())) selected @endif>
                                                    {{ $item->title }}
                                                </option>
                                                @if (count($item->subcategory))
                                                    @include(
                                                        'backend.service.components.sub-category-edit',
                                                        ['subcategories' => $item->subcategory]
                                                    )
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="inputStatus">Status
                                    </label>
                                    <select required="required" name="published" id="inputStatus"
                                        class="form-control custom-select">
                                        <option selected="" disabled="" value="">Select Option
                                        </option>&gt;
                                        <option @if ($service->published == true) selected @endif value="1">
                                            PUBLISHED
                                        </option>
                                        <option @if ($service->published == false) selected @endif value="0"> DRAFT
                                        </option>
                                    </select>
                                </div>
                                {{-- <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="disable_comment"
                                            name="disable_comment" value="1"
                                            @if ($service->disable_comment == true) checked @endif>
                                        <label class="custom-control-label" for="disable_comment">Disable Comments
                                        </label>
                                        <small>&nbsp;&nbsp;default is enabled
                                        </small>
                                    </div>
                                </div> --}}
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="featured"
                                            name="featured" value="1"
                                            @if ($service->featured == true) checked @endif>
                                        <label class="custom-control-label" for="featured">Featured
                                        </label>
                                        <br>
                                        <small>Featured will be shown on home page on priorty</small>
                                    </div>
                                </div>
                                <div class="form-group pt-0 pb-0 text-right">
                                    <button onclick="return confirm('Are you sure you want to update this Service?');"
                                        type="submit" class="btn btn-primary">Publish
                                    </button>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <div class="card card-info ">
                            <div class="card-header">
                                <h3 class="card-title">Featured Image
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                        title="Collapse">
                                        <i class="fas fa-minus" aria-hidden="true">
                                        </i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body pt-0 pb-0">
                                <div class="form-group">
                                    <small class="text-red">&nbsp;&nbsp;Note: size: Width-1200px Height: 800px
                                    </small>
                                    <input name="image" accept="image/*" type="file" id="imgInp">
                                    @if ($service->image)
                                        <img style="width: 175px; margin-top:10px; border:1px solid black;" id="blah"
                                            src="{{ asset('public/uploads/images/Service/' . $service->image) }}"
                                            alt="your image">
                                    @else
                                        <img style="width: 175px; margin-top:10px; border:1px solid black;" id="blah"
                                            src="{{ asset('public/uploads/images/no-image.jpg') }}" alt="your image">
                                    @endif
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </form>
    </div>

@stop

@section('css')
    <link rel="stylesheet" href="/public/css/admin_custom.css">
    <link href="{{ asset('public/ckeditor/plugins/codesnippet/lib/highlight/styles/default.css') }}" rel="stylesheet">

@stop

@section('js')
    <script src="{{ asset('public/ckeditor/ckeditor.js') }}"></script>
    {{-- view image while uploading --}}
    <script>
        imgInp.onchange = evt => {
            const [file] = imgInp.files
            if (file) {
                blah.src = URL.createObjectURL(file)
            }
        }
    </script>
    {{-- create live slug --}}
    <script>
        $('#title').on("change keyup paste click", function() {
            var Text = $(this).val().trim();
            Text = Text.toLowerCase();
            Text = Text.replace(/[^a-zA-Z0-9]+/g, '-');
            $('#slug').val(Text);
        });
    </script>
    <script>
        // In your Javascript (external .js resource or <script> tag)
        $(document).ready(function() {
            $('#tags').select2();
        });
    </script>

    {{-- for catgory  --}}
    <script>
        // In your Javascript (external .js resource or <script> tag)
        $(document).ready(function() {
            $('#category').select2({
                allowClear: true,
            });
        });
    </script>


    {{-- for auto hide alert message --}}
    <script>
        $(document).ready(function() {
            $(".alert").delay(6000).slideUp(300);
        });
    </script>


    {{-- ck editor image updoad --}}
    {{-- ck editor image updoad --}}
    <script>
        CKEDITOR.replace('editor', {
            filebrowserUploadUrl: "{{ route('ckeditor.upload', ['_token' => csrf_token()]) }}",
            filebrowserUploadMethod: "form",
            height: 500,
            allowedContent: true,
            removePlugins: 'iframe',
        });
    </script>

    {{-- Sucess and error notification alert --}}
    <script>
        $(document).ready(function() {
            // show error message
            @if ($errors->any())
                //var errorMessage = @json($errors->any()); // Get the first validation error message
                var Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 5500
                });

                Toast.fire({
                    icon: 'error',
                    title: 'There are form validation errors. Please fix them.'
                });
            @endif

            // success message
            @if (session('success'))
                var successMessage = @json(session('success')); // Get the first sucess message
                var Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 5500
                });

                Toast.fire({
                    icon: 'success',
                    title: successMessage
                });
            @endif

        });
    </script>
@stop
