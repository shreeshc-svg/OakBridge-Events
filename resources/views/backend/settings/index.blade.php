@extends('adminlte::page')

@section('title', 'Settings')

@section('content_header')
    <h1>Settings</h1>
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
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">
                    <!-- Profile Image -->
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <img class="w-50 mx-auto" src="{{ asset('public/uploads/images/logo/' . $setting->logo) }}"
                                    alt="">
                                <hr>
                                <span class="text-muted text-center text-dark h4">{{ $setting->bname }}</span> <br>
                            </div>
                            <!-- <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a> -->
                        </div>
                        <!-- /.card-body -->
                    </div>

                </div>
                <!-- /.col -->
                <div class="col-md-9">
                    <div class="card">
                        <form action="{{ route('setting.update') }}" method="post" enctype="multipart/form-data">
                            @csrf

                            <div class="card-header p-2">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"><a class="nav-link active" href="#business"
                                            data-toggle="tab">Business Information</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#social" data-toggle="tab">Social
                                            Media</a></li>
                                    <!-- <li class="nav-item"><a class="nav-link" href="#slider" data-toggle="tab">Home Page Slider</a></li> -->
                                    <li class="nav-item"><a class="nav-link" href="#google" data-toggle="tab">Google
                                            Analytics</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#map" data-toggle="tab">Google
                                            Map</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#seo" data-toggle="tab">SEO</a>
                                    </li>
                                    {{-- <li class="nav-item"><a class="nav-link" href="#security" data-toggle="tab">Security</a>
                                    </li> --}}
                                </ul>
                            </div><!-- /.card-header -->
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="active tab-pane" id="business">
                                        <!-- Personal -->

                                        <input type="hidden" class="form-control" name="id" id="inputName"
                                            value="">

                                        <div class="form-group row">
                                            <label for="inputName" class="col-sm-2 col-form-label">Business Name</label>
                                            <div class="col-sm-10">
                                                <input type="text"
                                                    class="form-control @error('bname') is-invalid @enderror" name="bname"
                                                    id="inputName" placeholder="Business Name"
                                                    value="{{ $setting->bname }}">
                                                @error('bname')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                                            <div class="col-sm-10">
                                                <input type="email"
                                                    class="form-control @error('email') is-invalid @enderror" name="email"
                                                    id="inputEmail" placeholder="Email" value="{{ $setting->email }}">
                                                @error('email')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputName2" class="col-sm-2 col-form-label">Phone</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="inputName2" name="phone"
                                                    placeholder="Contact Number" value="{{ $setting->phone }}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputName2" class="col-sm-2 col-form-label">Phone 2</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="inputName2" name="phone2"
                                                    placeholder="Contact Number 2" value="{{ $setting->phone2 }}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputName2" class="col-sm-2 col-form-label">WhatsApp</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="inputName2"
                                                    name="whatsapp" placeholder="WhatsApp Number"
                                                    value="{{ $setting->whatsapp }}">
                                                <small>Put whatsapp number with country code without space, Ex:
                                                    919865322154</small>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputName2" class="col-sm-2 col-form-label">Website
                                                Logo</label>
                                            <div class="col-sm-10">
                                                <input type="file" class="form-control" id="inputName2"
                                                    placeholder="Profiel Picture" name="logo">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputName2" class="col-sm-2 col-form-label">Address</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="inputName2"
                                                    placeholder="Address" value="{{ $setting->address }}"
                                                    name="address">
                                                <small>Adress will be visible on contact page</small>
                                            </div>
                                        </div>
                                        {{-- <div class="form-group row">
                                            <label for="inputName2" class="col-sm-2 col-form-label">Top Bar
                                                Content</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id=""
                                                    placeholder="Website Top Bar Deal Text"
                                                    value="{{ $setting->captcha_site_key }}" name="captcha_site_key">
                                                <small>Website Top Bar Deal Text</small>
                                            </div>
                                        </div> --}}

                                    </div>
                                    <!-- /.Social -->
                                    <div class="tab-pane" id="social">
                                        <div class="form-group row">
                                            <label for="inputName2" class="col-sm-2 col-form-label">Facebook</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="inputName2"
                                                    placeholder="Facebook" value="{{ $setting->facebook }}"
                                                    name="facebook">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputName2" class="col-sm-2 col-form-label">Instagram</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="inputName2"
                                                    placeholder="Instagram" value="{{ $setting->instagram }}"
                                                    name="instagram">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputName2" class="col-sm-2 col-form-label">Twitter</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="inputName2"
                                                    placeholder="twitter" value="{{ $setting->twitter }}"
                                                    name="twitter">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputName2" class="col-sm-2 col-form-label">Linkedin</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="inputName2"
                                                    placeholder="linkedin" value="{{ $setting->linkedin }}"
                                                    name="linkedin">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputName2" class="col-sm-2 col-form-label">Youtube</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="inputName2"
                                                    placeholder="Youtube" value="{{ $setting->youtube }}"
                                                    name="youtube">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.Social ends-->
                                    <!-- google analytics -->
                                    <div class="tab-pane" id="google">
                                        <div class="form-group row">
                                            <label for="inputName" class="col-sm-2 col-form-label">Google
                                                Analytics</label>
                                            <div class="col-sm-10">
                                                <small>Put Google analytics script here</small>
                                                <textarea name="gtag" id="" class="form-control" cols="30" rows="16">{{ $setting->gtag }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- google analytics ends-->
                                    <!-- google map -->
                                    <div class="tab-pane" id="map">
                                        <div class="form-group row">
                                            <label for="inputName" class="col-sm-2 col-form-label">Google
                                                Map</label>
                                            <div class="col-sm-10">
                                                <small>Put google map iframe code, keep in mind website contact page map
                                                    section height and width;</small>
                                                <textarea name="map" id="" class="form-control" cols="30" rows="8">{{ $setting->map }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="seo">
                                        <div class="form-group row">
                                            <label for="inputName2" class="col-sm-2 col-form-label">Website
                                                Title</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="inputName2"
                                                    name="site_title" placeholder="Site SEO Title"
                                                    value="{{ $setting->site_title }}">
                                                <small>Site title for google</small>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputName2" class="col-sm-2 col-form-label">Keywords</label>
                                            <div class="col-sm-10">

                                                <textarea name="site_keywords" id="" placeholder="Keyword1, keyword2, keyword3" class="form-control"
                                                    cols="30" rows="2">{{ $setting->site_keywords }}</textarea>
                                                <small>Put SEO keywords for your site</small>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputName2" class="col-sm-2 col-form-label">Site
                                                Description</label>
                                            <div class="col-sm-10">
                                                <textarea name="site_description" id="" placeholder="Website description..." class="form-control"
                                                    cols="30" rows="3">{{ $setting->site_description }}</textarea>
                                                <small>SEO description here</small>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- google map ends-->
                                    {{-- Google Captcha --}}
                                    {{-- <div class="tab-pane" id="security">
                                        <div class="form-group row">
                                            <label for="inputName2" class="col-sm-2 col-form-label">Google Captcha Site
                                                Key</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="inputName2"
                                                    name="captcha_site_key" placeholder="Captch Site Key"
                                                    value="{{ $setting->captcha_site_key }}">

                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputName2" class="col-sm-2 col-form-label">Google Captcha Secret
                                                Key</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="inputName2"
                                                    name="captch_secret_key" placeholder="Captch Secret Key"
                                                    value="{{ $setting->captch_secret_key }}">

                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="contact_form"
                                                    name="captcha_contact_form" value="1"
                                                    @if ($setting->captcha_contact_form == true) checked @endif>
                                                <label class="custom-control-label" for="contact_form">Disable/Enable
                                                    Captcha
                                                </label>
                                                <small>&nbsp;&nbsp;disable/enable the captcha for contact form on website
                                                </small>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="login_form"
                                                    name="captcha_login_form" value="1"
                                                    @if ($setting->captcha_login_form == true) checked @endif>
                                                <label class="custom-control-label" for="login_form">Disable/Enable
                                                    Captcha
                                                </label>
                                                <small>&nbsp;&nbsp;disable/enable the captcha for login page and forgot
                                                    password page
                                                </small>
                                            </div>
                                        </div>
                                    </div> --}}
                                    {{-- Google Captcha End --}}
                                    <!-- /.tab-pane -->
                                </div>
                                <!-- /.tab-content -->
                                <div class="form-group row">
                                    <div class="offset-sm-2 col-sm-10">
                                        <button onclick="return confirm('Are you sure you want to update settings?');"
                                            type="submit" class="btn btn-danger text-left">Update</button>
                                    </div>
                                </div>
                        </form>
                    </div><!-- /.card-body -->
                </div>
                <!-- /.card -->
                <!-- change password-->

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
    <script>
        $(document).ready(function() {
            $(".alert").delay(5000).slideUp(300);
        });
    </script>
@stop
