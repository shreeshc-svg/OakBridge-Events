@extends('adminlte::page')

@section('title', 'Edit Category')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Update Video</h1>
            <div class="pt-3">
                <a href="{{ route('video.index') }}" class="btn btn-primary ml-3"> Back</a>
            </div>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Video</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row px-3 justify-content-between">
            <div class="col-md-4">
                <form action="{{ route('video.update', $video->id) }}" method="post">
                    {{ csrf_field() }}
                    @method('PUT')
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Video title*</label>
                                    <input type="text" name="title" id="title"
                                        class="form-control @error('title') is-invalid @enderror" placeholder="Video Name"
                                        value="{{ old('title', $video->title) }}" />
                                    @error('title')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label bg-light">Youtube link <small>Unique url of the
                                            Youtube link</small></label>
                                    <input type="text" name="video"
                                        class="form-control bg-light @error('video') is-invalid @enderror"
                                        placeholder="https://www.youtube.com/watch?v=iQ08hjs6-Ng"
                                        value="{{ old('video', $video->video) }}">
                                    @error('video')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>


                        </div>

                    </div>
                    <div class="box-footer">
                        <button onclick="return confirm('Are you sure you want to update this item?');" type="submit"
                            class="btn btn-danger">Update</button>

                    </div>

                </form>


            </div>

        </div>
    </div>
@stop

@section('js')
    <script>
        $('#title').on("change keyup paste click", function() {
            var Text = $(this).val().trim();
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
