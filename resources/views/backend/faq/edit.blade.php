@extends('adminlte::page')

@section('title', 'Edit Faq')

@section('content_header')
    <div class="px-3">
        <h3>Edit Faq</h3>
        <div class="pt-3">
            <a href="{{ route('faq.index') }}" class="fw-bold ml-lg-4 btn btn-primary btn-sm">Go
                Back</a>
        </div>
    </div>
@stop

@section('content')
    <!--body -->
    <div class="container pb-5">
        <div class="row">
            <div class="col-md-8">
                <form method="POST" action="{{ route('faq.update', $faq->id) }}" enctype="multipart/form-data">
                    @csrf
                    {{ method_field('PUT') }}
                    <div class="form-group row">
                        <label for="staticEmail" class="col-sm-3 col-form-label">Name</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control @error('title') is-invalid  @enderror" name="title" id="title"
                                value="{{ $faq->title }}">
                                @error('title')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="staticEmail" class="col-sm-3 col-form-label">Description</label>
                        <div class="col-sm-9">
                            <textarea name="body" id="editor" class="form-control" cols="30" rows="8" value="{{ $faq->body }}">{{ $faq->body }}</textarea>
                            @error('body')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>



                    <div class="form-group row pt-3">
                        <label for="staticEmail" class="col-sm-3 col-form-label"></label>
                        <div class="col-sm-9">
                            <button class="btn btn-primary btn-sm" type="submit"
                                onclick="return confirm('Are you sure you want to update this item')">Update</button>

                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- /.content-header -->
@stop

@section('css')
    <link rel="stylesheet" href="/public/css/admin_custom.css">
@stop

@section('js')
    {{-- <script src="{{ asset('public/ckeditor/ckeditor.js') }}"></script> --}}

    <script src="https://cdn.ckeditor.com/4.22.1/standard-all/ckeditor.js"></script>

    {{-- ck editor image updoad --}}
    <script>
        CKEDITOR.replace('editor', {
            filebrowserUploadUrl: "{{ route('ckeditor.upload', ['_token' => csrf_token()]) }}",
            filebrowserUploadMethod: "form",
            height: 200,
            allowedContent: true,
            removePlugins: 'youtube,image',
        });
    </script>

    {{-- <script>
        CKEDITOR.replace('editor');
    </script> --}}

    <!-- this is for preview image while uploading -->
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
