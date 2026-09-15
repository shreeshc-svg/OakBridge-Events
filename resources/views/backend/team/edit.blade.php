@extends('adminlte::page')

@section('title', 'Add team member')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Edit Speaker</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Edit Speaker</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
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
        <form action="{{ route('team.update', $team->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="row">
                <div class="col-md-8">
                    <div class="card card-light">
                        <div class="card-header">
                            <h3 class="card-title">Add team
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
                                <label for="inputStatus">Name
                                </label>
                                <input class="form-control @error('name') is-invalid @enderror" type="text"
                                    id="title" name="name" placeholder="Name here.."
                                    value="{{ old('name', $team->name) }}">
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="inputStatus">Position
                                </label>
                                <input class="form-control @error('position') is-invalid @enderror" type="text"
                                    id="title" name="position" placeholder="Position here.."
                                    value="{{ old('position', $team->position) }}">
                                @error('position')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>
                        <!-- /.card-body -->
                    </div>

                    <div class="card card-light">
                        <div class="card-header">
                            <h3 class="card-title">Social Media
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
                                <label for="inputStatus" class="mb-0">Facebook
                                </label>
                                <input class="form-control" type="text" name="social[facebook]"
                                    placeholder="wwww.facebook.com/your-profile"
                                    value="{{ old('social.facebook', $team->social['facebook'] ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="inputStatus" class="mb-0">Instagram
                                </label>
                                <input class="form-control" type="text" name="social[instagram]"
                                    placeholder="wwww.instagram.com/your-profile"
                                    value="{{ old('social.instagram', $team->social['instagram'] ?? '') }}">
                            </div>

                            <div class="form-group">
                                <label for="inputStatus" class="mb-0">Linkedin
                                </label>
                                <input class="form-control" type="text" name="social[linkedin]"
                                    placeholder="wwww.linkedin.com/your-profile"
                                    value="{{ old('social.linkedin', $team->social['linkedin'] ?? '') }}">
                            </div>

                            <div class="form-group">
                                <label for="inputStatus" class="mb-0">X
                                </label>
                                <input class="form-control" type="text" name="social[x]"
                                    placeholder="wwww.x.com/your-profile"
                                    value="{{ old('social.x', $team->social['x'] ?? '') }}">
                            </div>

                            <div class="form-group">
                                <label for="inputStatus" class="mb-0">Youtube
                                </label>
                                <input class="form-control" type="text" name="social[youtube]"
                                    placeholder="wwww.youtube.com/your-profile"
                                    value="{{ old('social.youtube', $team->social['youtube'] ?? '') }}">
                            </div>

                        </div>
                        <!-- /.card-body -->
                    </div>

                    <div class="card card-light">
                        <div class="card-header">
                            <h3 class="card-title">About Speaker
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                    title="Collapse">
                                    <i class="fas fa-minus" aria-hidden="true">
                                    </i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <textarea style="height: 600px;" id="summernote" name="bio" value="{{ old('bio', $team->bio) }}"> {{ old('bio', $team->bio) }}</textarea>
                                        @error('bio')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-md-4">
                    <div class="sticky-top">
                        <div class="card card-info sticky-bottom">
                            <div class="card-header">
                                <h3 class="card-title">Team Details
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

                                <div class="form-group">
                                    <label class="mb-0" for="">Speaker/Advisor</label>
                                    <select name="year" class="form-control" aria-label="Default select example" required>
                                        <option value="">Choose option</option>
                                        <option value="Speaker" {{ old('year', $team->year) == 'Speaker' ? 'selected' : '' }}>
                                            Speaker</option>
                                        <option value="Advisor" {{ old('year', $team->year) == 'Advisor' ? 'selected' : '' }}>
                                            Advisor</option>
                                    </select>
                                    @error('year')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                </div>

                                <div class="form-group">
                                    <small class="text-red">&nbsp;&nbsp;Note: size: Width-500px Height: 500px
                                    </small>
                                    <input name="image" accept="image/*" type="file" id="imgInp">
                                    @if ($team->image)
                                        <img style="width: 175px; margin-top:10px; border:1px solid black;" id="blah"
                                        src="{{ asset('public/uploads/images/team/'.$team->image) }}" alt="your image">
                                        @else
                                        <img style="width: 175px; margin-top:10px; border:1px solid black;" id="blah"
                                        src="{{ asset('public/uploads/images/no-image.jpg') }}" alt="your image">

                                    @endif
                                </div>
                                <div class="form-group pt-0 pb-0 text-right">
                                    <button type="submit" class="btn btn-primary">Publish
                                    </button>
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
            var Text = $(this).val();
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


    {{-- for auto hide alert message --}}
    <script>
        $(document).ready(function() {
            $(".alert").delay(6000).slideUp(300);
        });
    </script>




@stop
