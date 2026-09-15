@extends('adminlte::page')

@section('title', 'All Services')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Events</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a> |</li>
                <li class=""> &nbsp; <a href="#">Events</a></li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="">
        @if (session('success'))
            <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>{{ session('success') }}</strong>
            </div>
        @endif
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
                                            <th style="width: 25%">
                                                Title
                                            </th>
                                            <th style="width: 10%">
                                                Image
                                            </th>
                                            {{-- <th style="width: 10%">
                                                Category
                                            </th> --}}
                                            {{--
                                            <th style="width: 10%">
                                                Featured
                                            </th> --}}


                                            <th style="width: 15%" class="text-center">
                                                Status
                                            </th>
                                            <th style="width: 12%">
                                                Action
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1; ?>
                                        @foreach ($services as $service)
                                            <tr>
                                                <td>
                                                    {{ $loop->iteration }}
                                                </td>
                                                <td>
                                                    <a>
                                                        {{ $service->title }}
                                                    </a>
                                                    <br>
                                                    <small>
                                                        {{ $service->created_at->diffForHumans() }}
                                                    </small>
                                                </td>
                                                <td>
                                                    @if ($service->image)
                                                        <img style="width:75px;"
                                                            src="{{ asset('public/uploads/images/service/' . $service->image) }}"
                                                            alt="">
                                                    @else
                                                        <img style="width:75px;"
                                                            src="{{ asset('public/uploads/images/no-image.jpg') }}"
                                                            alt="">
                                                    @endif
                                                </td>
                                                {{-- <td>
                                                    @foreach ($service->scategories as $category)
                                                        {{ $category->title }}@if (!$loop->last)
                                                            ,
                                                        @endif
                                                    @endforeach
                                                </td> --}}
                                                {{-- <td>
                                                    @if ($service->featured)
                                                        Yes
                                                    @else
                                                        No
                                                    @endif
                                                </td> --}}
                                                {{-- <td>
                                                    @if ($service->disable_comment)
                                                        Disabled
                                                    @else
                                                        Enabled
                                                    @endif
                                                </td> --}}
                                                <td class="project-state">
                                                    @if ($service->published)
                                                        <span class="badge badge-success">Published</span>
                                                    @else
                                                        <span class="badge badge-info">Draft</span>
                                                    @endif
                                                </td>
                                                <td class="project-actions text-right d-flex justify-content-between">
                                                    <div>
                                                        <a target="_blank" class="btn btn-primary btn-sm"
                                                            href="{{ route('service.detail', $service->slug) }}">
                                                            <i class="fas fa-folder">
                                                            </i>
                                                            View
                                                        </a>
                                                    </div>
                                                    <div>
                                                        <a class="btn btn-primary btn-sm mr-1" href="{{ route('schedules.edit', $service->id) }}">
                                                            <i class="fas fa-calendar-alt"></i> Schedule
                                                        </a>
                                                        <a class="btn btn-info btn-sm"
                                                            href="{{ route('service.edit', $service->id) }}">
                                                            <i class="fas fa-pencil-alt">
                                                            </i>
                                                            Edit
                                                        </a>
                                                    </div>
                                                    <div>
                                                        <form action="{{ route('service.destroy', $service->id) }}"
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
@endsection
