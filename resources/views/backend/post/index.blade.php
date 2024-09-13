@extends('adminlte::page')

@section('title', 'All Posts')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>All Posts</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('post.create') }}">+ Add New</a> |</li>
                <li class=""> &nbsp; <a href="{{ route('post.trash') }}">View Trash</a></li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <strong>{{ session('success') }}</strong>
        </div>
    @endif
    <div class="">
        <!-- Content Header (Page header) -->
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card py-2 px-2">

                            <div class="card-body p-0">
                                <table id="myTable" class="table table-striped projects ">
                                    <thead>
                                        <tr>
                                            <th style="width: 1%">
                                                #
                                            </th>
                                            <th style="width: 19%">
                                                Title
                                            </th>
                                            <th style="width: 10%">
                                                Image
                                            </th>
                                            <th style="width: 20%">
                                                Category
                                            </th>
                                            <th style="width: 10%">
                                                Featured
                                            </th>
                                            {{-- <th style="width: 10%">
                                                Comments
                                            </th> --}}
                                            <th style="width: 20%" class="text-center">
                                                Status
                                            </th>
                                            <th style="width: 21%">
                                                Action
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($posts as $post)
                                            <tr>
                                                <td>
                                                    {{ $loop->iteration }}
                                                </td>
                                                <td>
                                                    <a>
                                                        {{ $post->title }}
                                                    </a>
                                                    <br>
                                                    <small>
                                                        {{ $post->created_at->diffForHumans() }}
                                                    </small>
                                                </td>
                                                <td>
                                                    @if ($post->image)
                                                        <img style="width:75px;"
                                                            src="{{ asset('public/uploads/images/post/' . $post->image) }}"
                                                            alt="">
                                                    @else
                                                        <img style="width:75px;"
                                                            src="{{ asset('public/uploads/images/no-image.jpg') }}"
                                                            alt="">
                                                    @endif
                                                </td>
                                                <td>
                                                    @foreach ($post->categories as $category)
                                                        {{ $category->title }}@if (!$loop->last)
                                                            ,
                                                        @endif
                                                    @endforeach
                                                </td>
                                                <td>
                                                    @if ($post->featured)
                                                        Yes
                                                    @else
                                                        No
                                                    @endif
                                                </td>
                                                {{-- <td>
                                                    @if ($post->disable_comment)
                                                        Disabled
                                                    @else
                                                        Enabled
                                                    @endif
                                                </td> --}}
                                                <td class="project-state">
                                                    @if ($post->published)
                                                        <span class="badge badge-success">Published</span>
                                                    @else
                                                        <span class="badge badge-info">Draft</span>
                                                    @endif
                                                </td>
                                                <td class="project-actions text-right d-flex justify-content-between">
                                                    <div>
                                                        <a target="_blank" class="btn btn-primary btn-sm"
                                                            href="{{ route('blog.detail', $post->slug) }}">
                                                            <i class="fas fa-folder">
                                                            </i>
                                                            View
                                                        </a>
                                                    </div>
                                                    <div>
                                                        <a class="btn btn-info btn-sm"
                                                            href="{{ route('post.edit', $post->id) }}">
                                                            <i class="fas fa-pencil-alt">
                                                            </i>
                                                            Edit
                                                        </a>
                                                    </div>
                                                    <div>
                                                        <form action="{{ route('post.destroy', $post->id) }}"
                                                            method="post">
                                                            @csrf
                                                            @method('delete')
                                                            <button
                                                                onclick="return confirm('Are you sure you want to delete this item?');"
                                                                type="submit" class="btn btn-danger btn-sm">
                                                                <i class="fas fa-trash">
                                                                </i>
                                                                Trash
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
                    <!-- /.col -->

                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
    </div>
@stop

@section('css')

@stop

@section('js')

    {{-- hide notifcation --}}
    <script>
        $(document).ready(function() {
            $(".alert").delay(6000).slideUp(300);
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({
                responsive: true
            });

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
                    timer: 7500
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
                    timer: 6500
                });

                Toast.fire({
                    icon: 'success',
                    title: successMessage
                });
            @endif

        });
    </script>
@endsection
