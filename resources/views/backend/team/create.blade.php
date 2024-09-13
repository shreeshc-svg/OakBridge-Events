@extends('adminlte::page')

@section('title', 'Add team member')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Create Team</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Teams</li>
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
        <form action="{{ route('team.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row px-3 py-3">
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
                                <input class="form-control @error('name') is-invalid @enderror" type="text" id="title" name="name"
                                    placeholder="Name here.." value="{{ old('name') }}">
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                            </div>
                            <div class="form-group">
                                <label for="inputStatus">Position
                                </label>
                                <input class="form-control @error('position') is-invalid @enderror" type="text" id="title" name="position"
                                    placeholder="Position here.." value="{{ old('position') }}" >
                                    @error('position')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                            </div>

                        </div>
                        <!-- /.card-body -->
                    </div>

                    <div class="card card-light">
                        <div class="card-header">
                            <h3 class="card-title">About
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus" aria-hidden="true">
                                    </i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <textarea class="form-control w-full  @error('social') is-invalid @enderror" name="social" id="" cols="30" rows="15"></textarea>
                                        @error('social')
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
                                    <small class="text-red">&nbsp;&nbsp;Note: size: Width-500px Height: 500px
                                    </small>
                                    <input name="image" accept="image/*" type="file" id="imgInp">
                                    <img style="width: 175px; margin-top:10px; border:1px solid black;" id="blah"
                                        src="{{ asset('public/uploads/images/no-image.jpg') }}" alt="your image">
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

@section('js')
    {{-- ck editor image updoad --}}
    <script>
        CKEDITOR.replace('editor', {
            filebrowserUploadUrl: "{{ route('ckeditor.upload', ['_token' => csrf_token()]) }}",
            filebrowserUploadMethod: "form"
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


    {{-- ck editor image updoad --}}
    <script>
        CKEDITOR.replace('editor', {
            filebrowserUploadUrl: "{{ route('ckeditor.upload', ['_token' => csrf_token()]) }}",
            filebrowserUploadMethod: "form"
        });
    </script>

    {{-- Success and error notification alert --}}
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
