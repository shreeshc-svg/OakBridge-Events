@extends('adminlte::page')

@section('title', 'Gallery')

@section('content')
    <div class="container-fluid">
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
        <form action="{{ route('gallery.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row px-3 py-3">
                <div class="col-md-12">
                    <div class="card card-light">
                        <div class="card-header">
                            <h3 class="card-title">Add Gallery
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus" aria-hidden="true">
                                    </i>
                                </button>
                            </div>
                        </div>
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
                                                <div class="input-images"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group pt-0 pb-0 text-right">
                                <button type="submit" class="btn btn-primary">Publish
                                </button>
                            </div>
                        </div>

                        <!-- /.card-body -->
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
        $(document).ready(function() {
            $('.input-images').imageUploader({
                imagesInputName: 'src',
                preloadedInputName: 'old',
                maxSize: 8 * 1024 * 1024,
                maxFiles: 18
            });

        });
    </script>
@stop
