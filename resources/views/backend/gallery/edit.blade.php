@extends('adminlte::page')

@section('title', 'Gallery')

@section('content')
    <div class="container-fluid">

        <form action="{{ route('gallery.update') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row px-3 py-3">
                <div class="col-md-12">
                    <div class="card-body">
                        <div class="card card-light">
                            <div class="card-header">
                                <h3 class="card-title">Image Gallery</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"> <i
                                            class="fas fa-minus" aria-hidden="true"></i>
                                    </button>
                                </div>

                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="input-images">
                                                <img src="" alt="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group pt-0 pb-0 text-right">
                            <button type="submit" class="btn btn-primary">Update
                            </button>
                        </div>
                    </div>

                </div>
                <div class="col-md-4">
                    <div class="sticky-top">
                        <div class="card card-info sticky-bottom">


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
    {{-- multiple image uploader css --}}
    <link rel="stylesheet" href="{{ asset('public/image-uploader-master/dist/image-uploader.min.css') }}">
@stop

@section('js')
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
    <script>
        imgInp.onchange = evt => {
            const [file] = imgInp.files
            if (file) {
                blah.src = URL.createObjectURL(file)
            }
        }
    </script>


    <script>
        var preloaded = @json($galleries);
        $(document).ready(function() {
            $('.input-images').imageUploader({
                preloaded: preloaded,
                imagesInputName: 'src',
                preloadedInputName: 'old',
                maxSize: 2 * 1024 * 1024,
                maxFiles: 40
            });

        });
    </script>

    <!-- multiple image upload master -->
    <script src="{{ asset('public/image-uploader-master/dist/image-uploader.min.js') }}"></script>

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
