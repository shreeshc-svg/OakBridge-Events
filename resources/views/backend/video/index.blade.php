@extends('adminlte::page')

@section('title', 'Category')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Videos</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Videos</li>
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
                <h5>Add New Video
                </h5>
                <form role="form" method="post" action="{{ route('video.store') }}">
                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Video title*</label>
                                    <input type="text" name="title" id="title"
                                        class="form-control @error('title') is-invalid @enderror" placeholder="Video Name"
                                        value="{{ old('title') }}" />
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
                                        placeholder="https://www.youtube.com/watch?v=iQ08hjs6-Ng">
                                    @error('video')
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

                    <div id="" class="card-body p-0">
                        <table id="mytable" class="table table-striped projects">
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
                                    <th style="width: 29%">Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>


                                @foreach ($videos as $media)
                                    @php
                                        // Original YouTube watch URL
                                        $watchUrl = $media->video;

                                        // Parse the video ID from the URL
                                        $parsedUrl = parse_url($watchUrl);
                                        $embedUrl = null;
                                        if (isset($parsedUrl['query'])) {
                                            parse_str($parsedUrl['query'], $queryParams);
                                            if (isset($queryParams['v'])) {
                                                $embedUrl = 'https://www.youtube.com/embed/' . $queryParams['v'];
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            {{ $media->title }}
                                        </td>
                                        <td>
                                            @if ($embedUrl)
                                                <div style="">
                                                    <iframe width="75%" height="70" src="{{ $embedUrl }}"
                                                        frameborder="0"
                                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                        allowfullscreen>
                                                    </iframe>
                                                </div>
                                            @else
                                                <p>Invalid YouTube URL</p>
                                            @endif
                                        </td>

                                        <td class="project-actions text-right d-flex">

                                            <div>
                                                <a class="btn btn-info btn-sm"
                                                    href="{{ route('video.edit', $media->id) }}">
                                                    <i class="fas fa-pencil-alt">
                                                    </i>
                                                    Edit
                                                </a>
                                            </div>
                                            <div>
                                                <form action="{{ route('video.destroy', $media->id) }}" method="POST">
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
    {{-- Slug --}}
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
