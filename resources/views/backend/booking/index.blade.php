@extends('adminlte::page')

@section('title', 'Category')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Bookings</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Bookings</li>
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
            <div class="col-md-12">
                <div class="card p-2">

                    <div id="" class="card-body p-0">
                        <table id="mytable" class="table table-striped projects">
                            <thead>
                                <tr>
                                    <th style="width: 1%">
                                        #
                                    </th>
                                    <th style="width: 14%">
                                        Booking id
                                    </th>
                                    <th style="width: 20%">
                                        Name
                                    </th>

                                    <th style="width: 10%">
                                        Email
                                    </th>

                                    <th style="width: 10%">
                                        Phone
                                    </th>
                                    
                                     <th style="width: 10%">
                                        Company
                                    </th>
                                      <th style="width: 10%">
                                        Designation
                                    </th>
                                    <th style="width: 15%">
                                        Event
                                    </th>
                                    <th style="width: 10%">
                                        Date
                                    </th>
                                    <th style="width: 29%">Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bookings as $booking)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $booking->booking_id }}</td>
                                        <td>
                                            <a>
                                                {{ $booking->name }}
                                            </a>
                                            <br>
                                            <small>Booked on: {{ $booking->created_at->format('d M Y') }}</small>

                                        </td>
                                        <td>
                                            {{ $booking->email }}
                                        </td>
                                        <td>
                                            {{ $booking->phone }}
                                        </td>
                                        
                                            <td>
                                            {{ $booking->company }}
                                        </td>
                                        
                                         <td>
                                            {{ $booking->designation }}
                                        </td>


                                        <td>
                                            {{ $booking->event ?: '—' }}
                                        </td>
                                        <td>
                                            {{ $booking->date }}
                                        </td>


                                        <td class="project-actions text-right d-flex">

                                            <div>
                                                <form action="{{ route('booking.destroy', $booking->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        onclick="return confirm('Category cannot be delted - Post attached');"
                                                        class="btn btn-danger btn-sm ml-2">
                                                        <i class="fas fa-trash"></i>
                                                        Delete
                                                        </a>
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

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
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
            $('#mytable').DataTable({
                dom: 'Bfrtip', // Define button container
                buttons: [
                    'csv', // Enable the CSV export button
                ],
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
