@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Profile</h1>
@stop

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">
                    <!-- Profile Image -->
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                @if (Auth::user()->profile_pic)
                                    <img class="profile-user-img img-fluid img-circle"
                                        src="/uploads/images/agent/profile_pic/{{ Auth::user()->profile_pic }}"
                                        alt="User profile picture">
                                @else
                                    <i class="fa fa-user" aria-hidden="true"></i>
                                @endif
                            </div>

                            <h3 class="profile-username text-center">{{ Auth::user()->name }}</h3>

                            <p class="text-muted text-center">{{ Auth::user()->email }}</p>



                            <!-- <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a> -->
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
                <div class="col-md-9">
                    <div class="card">
                        <form action="{{ route('profile.update') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="card-header p-2">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"><a class="nav-link active" href="#personal"
                                            data-toggle="tab">Personal Information</a></li>
                                    <!-- <li class="nav-item"><a class="nav-link" href="#social" data-toggle="tab">Social Media</a></li> -->
                                </ul>
                            </div><!-- /.card-header -->
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="active tab-pane" id="personal">
                                        <!-- Personal -->

                                        <div class="form-group row">
                                            <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="inputName"
                                                    placeholder="Name" value="{{ Auth::user()->name }}">
                                                    @error('name')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                                            <div class="col-sm-10">
                                                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" id="inputEmail"
                                                    placeholder="Email" value="{{ Auth::user()->email }}">
                                                    @error('email')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                            </div>
                                        </div>
                                        <!-- /.Personal -->
                                    </div>
                                    <!-- /.Social -->
                                    <!-- /.tab-pane -->
                                </div>
                                <!-- /.tab-content -->
                                <div class="form-group row">
                                    <div class="offset-sm-2 col-sm-10">
                                        <button onclick="return confirm('Are you sure you want to update profile?');"
                                            type="submit" class="btn btn-danger">Update</button>
                                    </div>
                                </div>
                        </form>
                    </div><!-- /.card-body -->
                </div>
                <!-- /.card -->
                <!-- change password-->
                <div class="card">
                    <form action="{{ route('password.update') }}" method="post">
                        @csrf
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item"><a class="nav-link active" href="#password"
                                        data-toggle="tab">Change Password</a></li>
                            </ul>
                        </div><!-- /.card-header -->
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="active tab-pane" id="#password">
                                    <!-- Password -->
                                    <div class="tab-pane" id="password">
                                        <div class="form-group row">
                                            <label for="inputName" class="col-sm-2 col-form-label">Old
                                                Password</label>
                                            <div class="col-sm-10">
                                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="inputName"
                                                    placeholder="Old Password" name="current_password"
                                                    autocomplete="current_password">

                                                    @error('current_password')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputEmail" class="col-sm-2 col-form-label">New
                                                Password</label>
                                            <div class="col-sm-10">
                                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="inputEmail"
                                                    placeholder="New Password" name="password"
                                                    autocomplete="password_confirmation">
                                                    @error('password')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputName2" class="col-sm-2 col-form-label">Confirm
                                                Password</label>
                                            <div class="col-sm-10">
                                                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="inputName2"
                                                    placeholder="Confirm Password" name="password_confirmation"
                                                    autocomplete="password_confirmation">
                                                    @error('password_confirmation')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.tab-pane -->
                                </div>
                                <!-- /.tab-content -->
                                <div class="form-group row">
                                    <div class="offset-sm-2 col-sm-10">
                                        <button onclick="return confirm('Are you sure you want to update profile?');"
                                            type="submit" class="btn btn-danger">Update</button>
                                    </div>
                                </div>
                    </form>
                </div><!-- /.card-body -->
            </div>
            <!-- change password ends--->
        </div>
        <!-- /.col -->

        </div>
        <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        console.log('Hi!');
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
