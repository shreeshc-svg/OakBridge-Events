@extends('adminlte::page')

@section('title', 'Edit Testimonials')

@section('content_header')
    <div class="row mb-2 px-3">
        <div class="col-sm-6">
            <h1 class="m-0">Edit testimonial</h1>
            <div class="pt-3">
                <a href="{{ route('testimonial.index') }}" class="fw-bold ml-lg-4 btn btn-primary btn-sm">Go
                    Back</a>
            </div>
        </div>
        <!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Edit testimonial</li>
            </ol>
        </div>
        <!-- /.col -->
    </div>
@stop

@section('content')
    <div class="content-header">
        <!--body -->
        <div class="container  pb-5">
            <div class="row">
                <div class="col-md-8">
                    <form method="POST" action="{{ route('testimonial.update', $testimonial->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        {{ method_field('PUT') }}
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">Name</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                    name="title" id="title" value="{{ $testimonial->title }}">
                                @error('title')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">Location</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('location') is-invalid @enderror"
                                    name="location" id="testimonial" value="{{ $testimonial->location }}">
                                @error('location')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">Stars</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('star') is-invalid @enderror"
                                    name="star" id="star" value="{{ $testimonial->star }}">
                                @error('star')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-9">
                                <input type="text" class="form-control bg-light" name="slug" id="slug"
                                    value="{{ $testimonial->slug }}" hidden>

                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">Description</label>
                            <div class="col-sm-9">
                                <textarea name="body" id="" class="form-control" cols="30" rows="3"
                                    value="{{ $testimonial->body }}">{{ $testimonial->body }}</textarea>
                            </div>
                        </div>
                        {{-- <div class="form-group row pt-3">
                            <label for="staticEmail" class="col-sm-3 col-form-label">Featured</label>
                            <div class="col-sm-9">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="customSwitch1" name="featured"
                                        {{ $testimonial->featured ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="customSwitch1">Featured</label>
                                </div>
                                <span class="text-muted text-sm">Featured testimonial will be shown on home page as
                                    priorty.</span>
                            </div>
                        </div> --}}
                        <div class="form-group row pt-3">
                            <label for="staticEmail" class="col-sm-3 col-form-label">Image</label>
                            <div class="col-sm-9">
                                <input name="image" class="form-control input-sm" accept="image/*" type='file'
                                    id="imgInp" />
                                <span class="text-muted text-sm">Recommended image size is 720px by 560px.</span><br>
                                <img style="width: 180px; margin-top:10px; border:1px solid black;" id="blah"
                                    src="@if ($testimonial->image) {{ asset('public/uploads/images/testimonial/' . $testimonial->image) }} @else {{ asset('public/uploads/images/no-image.jpg') }} @endif"
                                    alt="your image" />
                            </div>
                        </div>
                        <div class="form-group row pt-3">
                            <label for="staticEmail" class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                                <button class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to update this item?')"
                                    type="submit">Update</button>

                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
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
