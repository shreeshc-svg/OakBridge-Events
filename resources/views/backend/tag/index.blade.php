@extends('adminlte::page')

@section('title', 'Tags')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Tag</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Tag</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row px-3 pt-3 justify-content-between">
            <div class="col-md-4 ">
                <h5>Add New tag
                </h5>
                <form role="form" method="post">
                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>tag name*</label>
                                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
                                        placeholder="tag Name" value="{{ old('title') }}" >
                                        @error('title')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="slug" class="form-label bg-light">Slug <small>Unique url of the
                                            tag</small></label>
                                    <input type="text" name="slug" class="form-control bg-light @error('slug') is-invalid @enderror" id="slug">
                                    @error('slug')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
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

                    <div class="card-body p-0">
                        <table id="my-table" class="table table-striped projects">
                            <thead>
                                <tr>
                                    <th style="width: 1%">
                                        #
                                    </th>
                                    <th style="width: 30%">
                                        Name
                                    </th>
                                    <th style="width: 30%">
                                        Slug
                                    </th>
                                    <th style="width: 9%">
                                        Post Count
                                    </th>
                                    <th style="width: 29%">Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>

                                @foreach ($tags as $tag)
                                    <tr>
                                        <td>
                                            {{ $i++ }}
                                        </td>
                                        <td>
                                            <a>
                                                {{ $tag->title }}
                                            </a>

                                        </td>
                                        <td>
                                            <a>{{ $tag->slug }}</a>
                                        </td>
                                        <td>
                                            {{ $tag->posts->count() }}

                                        </td>

                                        <td class="project-actions text-right d-flex">

                                            <div>
                                                <a class="btn btn-info btn-sm" href="{{ route('tag.edit', $tag->id) }}">
                                                    <i class="fas fa-pencil-alt">
                                                    </i>
                                                    Edit
                                                </a>
                                            </div>
                                            <div>
                                                <form action="{{ route('tag.destroy', $tag->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        onclick="return confirm('Are you sure you want to delete this item?');"
                                                        class="btn btn-danger btn-sm ml-2">
                                                        <i class="fas fa-trash"></i>
                                                        Delete
                                                        </a>
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
    {{-- Datatable --}}
    <script>
        $(document).ready(function() {
            $('#my-table').DataTable();
        });
    </script>

    {{-- Slug --}}
    <script>
        $('#title').on("change keyup paste click", function() {
            var Text = $(this).val().trim();
            Text = Text.toLowerCase();
            Text = Text.replace(/[^a-zA-Z0-9]+/g, '-');
            $('#slug').val(Text);
        });
    </script>

    {{-- Succes and error notification alert --}}
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
