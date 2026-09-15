@extends('adminlte::page')

@section('title', 'Edit Category')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Update Vidhi Samman</h1>
            <div class="pt-3">
                <a href="{{ route('vidhi.index') }}" class="btn btn-primary ml-3"> Back</a>
            </div>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Vidhi Samman</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row px-3 justify-content-between">
            <div class="col-md-4">
                <form role="form" method="post" action="{{ route('vidhi.update', $image->id) }}"
                    enctype="multipart/form-data">
                    {{ csrf_field() }}

                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="mb-0" for="">Year</label>
                                    <select name="year" class="form-control" aria-label="Default select example">
                                        <option value="">Choose Year</option>
                                        <option value="2024" {{ old('year', $image->year) == '2024' ? 'selected' : '' }}>
                                            2024</option>
                                        <option value="2025" {{ old('year', $image->year) == '2025' ? 'selected' : '' }}>
                                            2025</option>
                                    </select>
                                    @error('year')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="mb-0" for="">Category</label>
                                    <select name="category" class="form-control" aria-label="Default select example">
                                        <option value="">Choose Year</option>
                                        <option value="Sarvoch Vidhi Samman"
                                            {{ old('category', $image->category) == 'Sarvoch Vidhi Samman' ? 'selected' : '' }}>
                                            Sarvoch Vidhi Samman</option>
                                        <option value="Vishist Vidhi Samman"
                                            {{ old('category', $image->category) == 'Vishist Vidhi Samman' ? 'selected' : '' }}>
                                            Vishist Vidhi Samman</option>
                                        <option value="Vidhi Samman"
                                            {{ old('category', $image->category) == 'Vidhi Samman' ? 'selected' : '' }}>
                                            Vidhi
                                            Samman
                                        </option>
                                    </select>
                                    @error('year')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="mb-0">Title</label>
                                    <input type="text" name="title" id="title"
                                        class="form-control @error('title') is-invalid @enderror" placeholder="Title"
                                        value="{{ old('title', $image->title) }}" />
                                    @error('title')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="mb-0">Description</label>
                                    <textarea name="body" class="form-control" id="" cols="30" rows="2"
                                        value="{{ old('body', $image->body) }}" placeholder="Sub headline here...">{{ old('body', $image->body) }}</textarea>
                                    @error('body')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="form-group">
                                        <small class="text-red">&nbsp;&nbsp;Note: size: Width-1200px Height: 800px
                                        </small>
                                        <input name="image" accept="image/*" type="file" id="imgInp">
                                        @if ($image->image)
                                            <img style="width: 175px; margin-top:10px; border:1px solid black;"
                                                id="blah"
                                                src="{{ asset('public/uploads/images/vidhi/' . $image->image) }}"
                                                alt="your image">
                                        @else
                                            <img style="width: 175px; margin-top:10px; border:1px solid black;"
                                                id="blah" src="{{ asset('public/uploads/images/no-image.jpg') }}"
                                                alt="your image">
                                        @endif
                                    </div>
                                </div>
                            </div>



                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-danger"
                            onclick="return confirm('Are you sure you want to update this item?')">Update</button>

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

    <script>
        imgInp.onchange = evt => {
            const [file] = imgInp.files
            if (file) {
                blah.src = URL.createObjectURL(file)
            }
        }
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
