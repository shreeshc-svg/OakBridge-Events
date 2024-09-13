@extends('adminlte::page')

@section('title', 'All Faq')

@section('content')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>All faqs</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active">faqs</li>
            </ol>
        </div>
    </div>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="pb-3">
                        <a href="{{ route('faq.create') }}" class="btn btn-primary rounded-1">+ Add
                            New</a>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            @if ($faqs)
                                <h3 class="card-title">Total Result Found: {{ $faqs->count() }}</h3>
                            @endif
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-2">
                            <table id="faqtable" class="table table-striped projects">
                                <thead>
                                    <tr>
                                        <th style="width: 1%">
                                            #
                                        </th>
                                        <th style="width: 29%">
                                            Title
                                        </th>
                                        <th style="width: 50%">
                                            Content
                                        </th>
                                        <th style="width: 20%" class="text-center">
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($faqs as $faq)
                                        <tr>
                                            <td>
                                                {{ $loop->iteration }}
                                            </td>
                                            <td>
                                                <a>
                                                    {{ $faq->title }}
                                                </a>
                                                <br>
                                                <small>
                                                    {{ $faq->created_at->diffForHumans() }}
                                                </small>
                                            </td>


                                            <td>
                                                {!! Str::limit($faq->body, 150) !!}
                                            </td>

                                            <td class="project-actions text-right d-flex">

                                                <div>
                                                    <a class="btn btn-info btn-sm" href="{{ route('faq.edit', $faq->id) }}">
                                                        <i class="fas fa-pencil-alt">
                                                        </i>
                                                        Edit
                                                    </a>
                                                </div>
                                                <div>
                                                    <form action="{{ route('faq.destroy', $faq->id) }}" method="POST">
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
                            <div class=" pt-3 px-3">
                                {{-- {{ $faqs->links() }} --}}
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                </div>
                <!-- /.col -->

            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>

    <!-- Control Sidebar -->

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


    {{-- data table --}}
    <script>
        $(document).ready(function() {
            $('#faqtable').DataTable({
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
