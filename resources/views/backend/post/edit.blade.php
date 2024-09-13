@extends('adminlte::page')

@section('title', 'Edit Post')

@section('content_header')
    <div class="d-flex justify-content-between px-md-3 align-items-center">
        <div>
            <a href="{{ route('post.index') }}" class="btn btn-primary">Back</a>
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
        @if ($errors->any())
            <div class="mt-3">
                @foreach ($errors->all() as $error)
                    <li class="alert alert-danger">{{ $error }}</li>
                @endforeach
            </div>
        @endif
        <form action="{{ route('post.update', $post->id) }}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            @method('PUT')
            <div class="row px-3 py-3">
                <div class="col-md-8">
                    <div class="card card-light">
                        <div class="card-header">
                            <h3 class="card-title">Edit Post
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
                                    id="title" name="title" placeholder="Title here.." value="{{ $post->title }}">
                                @error('title')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="inputStatus">Slug
                                </label>
                                <small>&nbsp;&nbsp;Unique url of the post
                                </small>
                                <input class="form-control bg-light" type="text" id="slug" name="slug"
                                    placeholder="slug here.." value="{{ $post->slug }}" required="">
                                @error('slug')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="">Post Description
                                </label>
                                @error('body')
                                    <li class="text-danger">{{ $error }}</li>
                                @enderror
                                <textarea style="height: 600px;" id="editor" name="body" value="{{ $post->body }}" name="body" required />{{ $post->body }}</textarea>
                                @error('body')
                                    <span class="text-danger">{{ $message }}</span>
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
                                    value="{{ $post->video }}">
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div> --}}
                    <div class="card card-light">
                        <div class="card-header">
                            <h3 class="card-title">Excerpt
                            </h3>
                            <small>&nbsp;&nbsp;Small Description of the post
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
                                <textarea class="form-control" name="excerpt" id="" value="{{ $post->excerpt }}" cols="30"
                                    rows="5">{{ $post->excerpt }}</textarea>
                            </div>
                        </div>
                        <!-- /.card-body -->
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
                                <input placeholder="Post title here for seo..." type="text" class="form-control"
                                    name="meta_title" id="" value="{{ $post->meta_title }}">
                            </div>
                        </div>
                        <div class="card-body  pt-0 pb-0">
                            <div class="form-group">
                                <label for="">SEO Description
                                </label>
                                <textarea placeholder="Post description here for seo..." class="form-control" name="meta_description" id=""
                                    cols="0" rows="4" value="{{ $post->meta_description }}">{{ $post->meta_description }}</textarea>
                            </div>
                        </div>
                        <div class="card-body pt-0 pb-0">
                            <div class="form-group">
                                <label for="">SEO Keywords
                                </label>
                                <input type="text" class="form-control" placeholder="keyword1, keyword2, keyword3"
                                    name="meta_keyword" id="" value="{{ $post->meta_keyword }}">
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="form-group select2-primary">
                                <label for="">SEO Tags
                                </label>
                                <select id="tags" name="tag[]" class="select2" multiple="multiple"
                                    data-placeholder="Search Tags" style="width: 100%;">
                                    @foreach ($tags as $tag)
                                        <option value="{{ $tag->id }}"
                                            {{ $post->tags->contains('id', $tag->id) ? 'selected' : '' }}>
                                            {{ $tag->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="sticky-top">
                        <div class="card card-info sticky-bottom">
                            <div class="card-header">
                                <h3 class="card-title">Post Details
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
                                    {{-- <select id="category" name="category[]" class="select2" multiple="multiple"
                                        data-placeholder="Search Category" style="width: 100%;">
                                        </option>&gt;
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ $post->categories->contains('id', $category->id) ? 'selected' : '' }}>
                                                {{ $category->title }}
                                            </option>
                                        @endforeach
                                    </select> --}}

                                    <select id="category" name="category[]" class="select2" multiple="multiple"
                                        data-placeholder="Search Category" style="width: 100%;">
                                        <option value="">None</option>
                                        @if ($categories)
                                            @foreach ($categories as $item)
                                                <?php $dash = ''; ?>
                                                <option value="{{ $item->id }}"
                                                    @if (in_array($item->id, $post->categories->pluck('id')->toArray())) selected @endif>
                                                    {{ $item->title }}
                                                </option>
                                                @if (count($item->subcategory))
                                                    @include(
                                                        'backend.post.components.sub-category-list-edit',
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
                                        <option @if ($post->published == true) selected @endif value="1">
                                            PUBLISHED
                                        </option>
                                        <option @if ($post->published == false) selected @endif value="0"> DRAFT
                                        </option>
                                    </select>
                                </div>
                                {{-- <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="disable_comment"
                                            name="disable_comment" value="1"
                                            @if ($post->disable_comment == true) checked @endif>
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
                                            @if ($post->featured == true) checked @endif>
                                        <label class="custom-control-label" for="featured">Featured
                                        </label>
                                        <br>
                                        <small>Featured will be shown on home page on priorty</small>
                                    </div>
                                </div>
                                <div class="form-group pt-0 pb-0 text-right">
                                    <button onclick="return confirm('Are you sure you want to update this post?');"
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
                                    @if ($post->image)
                                        <img style="width: 175px; margin-top:10px; border:1px solid black;" id="blah"
                                            src="{{ asset('public/uploads/images/post/' . $post->image) }}"
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
                maximumSelectionLength: 1
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

    {{-- Success and error notification --}}
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
