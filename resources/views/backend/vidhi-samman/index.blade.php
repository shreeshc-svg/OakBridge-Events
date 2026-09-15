@extends('adminlte::page')

@section('title', 'Vidhi Samman')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Vidhi Samman</h1>
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
    @if (session('success'))
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <strong>{{ session('success') }}</strong>
        </div>
    @endif
    <div class="container-fluid">
        <div class="row justify-content-between">
            <div class="col-md-4 ">
                <h5>Add New Vidhi Samman
                </h5>
                <form role="form" method="post" action="{{ route('vidhi.store') }}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="mb-0" for="">Year</label>
                                    <select name="year" class="form-control" aria-label="Default select example">
                                        <option value="">Choose Year</option>
                                        <option value="2024" {{ old('year') == '2024' ? 'selected' : '' }}>2024</option>
                                        <option value="2025" {{ old('year') == '2025' ? 'selected' : '' }}>2025</option>
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
                                            {{ old('category') == 'Sarvoch Vidhi Samman' ? 'selected' : '' }}>
                                            Sarvoch Vidhi Samman</option>
                                        <option value="Vishist Vidhi Samman"
                                            {{ old('category') == 'Vishist Vidhi Samman' ? 'selected' : '' }}>
                                            Vishist Vidhi Samman</option>
                                        <option value="Vidhi Samman"
                                            {{ old('category') == 'Vidhi Samman' ? 'selected' : '' }}>Vidhi Samman
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
                                        value="{{ old('title') }}" />
                                    @error('title')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="mb-0">Description</label>
                                    <textarea name="body" class="form-control" id="" cols="30" rows="2" value="{{ old('body') }}"
                                        placeholder="Sub headline here...">{{ old('body') }}</textarea>
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
                                        <img style="width: 100px; margin-top:10px; border:1px solid black;" id="blah"
                                            src="{{ asset('public/uploads/images/no-image.jpg') }}" alt="your image">
                                        @error('image')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>



                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Create</button>

                    </div>
                </form>


            </div>
            <div class="col-md-8 pt-4 pl-lg-5">
                <div class="card p-2">

                    <div id="" class="card-body p-0">
                        <table id="mytable" class="table table-striped projects">
                            <thead>
                                <tr>
                                    <th style="width: 1%">
                                        #
                                    </th>
                                    <th style="width: 20%">
                                        Image
                                    </th>
                                    <th style="width: 20%">
                                        Title
                                    </th>
                                    <th style="width: 10%">
                                        Year
                                    </th>
                                    <th style="width: 20%">
                                        Category
                                    </th>
                                    <th style="width: 20%">Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>


                                @foreach ($images as $image)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>

                                        <td>
                                            <img width="100"
                                                src="{{ asset('public/uploads/images/vidhi/' . $image->image) }}"
                                                alt="">
                                        </td>
                                        <td>
                                            {{ $image->title }}
                                        </td>
                                        <td>
                                            {{ $image->year }}
                                        </td>
                                        <td>
                                            {{ $image->category }}
                                        </td>


                                        <td class="project-actions text-right d-flex">

                                            <div>
                                                <a class="btn btn-info btn-sm"
                                                    href="{{ route('vidhi.edit', $image->id) }}">
                                                    <i class="fas fa-pencil-alt">
                                                    </i>
                                                    Edit
                                                </a>
                                            </div>
                                            <div>
                                                <form action="{{ route('vidhi.destroy', $image->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        onclick="return confirm('Are you sure you want to remove this item?');"
                                                        class="btn btn-danger btn-sm ml-2">
                                                        <i class="fas fa-trash"></i>
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
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
    <script>
        $('#title').on("change keyup paste click", function() {
            var Text = $(this).val().trim();
            Text = Text.toLowerCase();
            Text = Text.replace(/[^a-zA-Z0-9]+/g, '-');
            $('#slug').val(Text);
        });
    </script>

    {{-- Data table --}}
    <script>
        $(document).ready(function() {
            $('#mytable').DataTable();
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
