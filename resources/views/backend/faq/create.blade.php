@extends('adminlte::page')

@section('title', 'Faq')

@section('content')

    <div class="container-fluid">
        <form action="{{ route('faq.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row px-3 py-3">
                <div class="col-md-8">
                    <div class="card card-light">
                        <div class="card-header">
                            <h3 class="card-title">Add Frequently asked questions
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
                                <input class="form-control @error('title') is-invalid @enderror" type="text" id="title" name="title"
                                    placeholder="Title here.." value="{{ old('title') }}" >
                                    @error('title')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                            </div>
                            <div class="form-group">
                                <input class="form-control" type="text" id="slug" name="slug"
                                    placeholder="Title here.." value="" hidden>
                            </div>

                            <div class="form-group">
                                <label for="inputStatus">Content
                                </label>
                                <small>&nbsp;&nbsp;Answer of the question
                                </small>
                                <textarea class="form-control @error('body') is-invalid @enderror" name="body" id="editor" placeholder="Answer Here..." value="{{ old('body') }}"
                                    cols="30" rows="8">{{ old('body') }}</textarea>
                                    @error('body')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="form-group pt-0 pb-0 text-right px-4">
                            <button type="submit" class="btn btn-primary">Publish
                            </button>
                        </div>
                    </div>


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
    {{-- <script src="{{ asset('public/ckeditor/ckeditor.js') }}"></script> --}}

    <script src="https://cdn.ckeditor.com/4.22.1/standard-all/ckeditor.js"></script>

    {{-- ck editor image updoad --}}
    <script>
        CKEDITOR.replace('editor', {
            filebrowserUploadUrl: "{{ route('ckeditor.upload', ['_token' => csrf_token()]) }}",
            filebrowserUploadMethod: "form",
            height: 300,
            allowedContent: true
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
