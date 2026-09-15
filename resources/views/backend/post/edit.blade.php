@extends('adminlte::page')

@section('title', 'Edit Post')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Post Edit</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Post Edit</li>
            </ol>
        </div>
    </div>
@stop

@section('content')

    <div class="container-fluid ">
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
            <div class="row">
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

                                <textarea style="height: 600px;" id="summernote" name="body" value="{{ $post->body }}" name="body" required />{{ $post->body }}</textarea>
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
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <style>
        /* summer note */
        .modal-header .close,
        .modal-header .mailbox-attachment-close {
            padding: 0rem;
            margin: 0 auto;
        }

        .modal-header {
            display: -ms-flexbox;
            display: block;
            -ms-flex-align: start;
            align-items: flex-start;
            -ms-flex-pack: justify;
            justify-content: space-between;
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
            border-top-left-radius: calc(0.3rem - 1px);
            border-top-right-radius: calc(0.3rem - 1px);
        }
    </style>


@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

    {{-- summer note --}}
    <script>
        $(document).ready(function() {
            $('#summernote').summernote({
                height: 400,

                callbacks: {
                    onImageUpload: function(files) {
                        uploadImage(files[0]);
                    },
                    onMediaDelete: function(target) {
                        deleteImage(target[0].src);
                        if (target[0].nodeName === 'VIDEO') {
                            // Check if the deleted element is a video
                            target.remove(); // Remove the video element
                        }
                    },

                }
            });

            function uploadImage(file) {
                let formData = new FormData();
                formData.append('image', file);

                $.ajax({
                    url: '{{ route('summer.upload.image') }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        let imageUrl = response.url;
                        $('#summernote').summernote('editor.insertImage', imageUrl);
                    },
                    error: function(error) {
                        console.error(error);
                    }
                });
            }

            function deleteImage(imageSrc) {
                console.log('Deleting image with source URL:', imageSrc);

                $.ajax({
                    url: '{{ route('summer.delete.image') }}',
                    type: 'POST',
                    data: {
                        imageSrc: imageSrc
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log(response.message);
                    },
                    error: function(error) {
                        console.error(error);
                    }
                });
            }

        });
    </script>
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

@stop
