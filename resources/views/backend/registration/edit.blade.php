@extends('adminlte::page')

@section('title', 'Registration')

@section('content_header')
    <h1>Registration</h1>
@stop

@section('content')
    @php
        $isOpen = (bool) old('registration_enabled', $setting->registration_enabled ?? 1);
        $defaultLabel = \App\Http\Controllers\RegistrationController::DEFAULT_BUTTON_TEXT;
        $defaultMessage = \App\Http\Controllers\RegistrationController::DEFAULT_CLOSED_MESSAGE;
    @endphp

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Please fix the following:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('registration.update') }}" method="post">
        @csrf
        <div class="row">
            <div class="col-lg-7">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Event registration</h3>
                        <span class="badge float-right {{ ($setting->registration_enabled ?? 1) ? 'badge-success' : 'badge-secondary' }}">
                            Currently {{ ($setting->registration_enabled ?? 1) ? 'open' : 'closed' }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="custom-control custom-switch custom-switch-lg mb-3">
                            <input type="hidden" name="registration_enabled" value="0">
                            <input type="checkbox" class="custom-control-input" id="registration_enabled"
                                name="registration_enabled" value="1" {{ $isOpen ? 'checked' : '' }}>
                            <label class="custom-control-label font-weight-bold" for="registration_enabled">
                                Registration open
                            </label>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1 text-success font-weight-bold">When ON</p>
                                <ul class="small pl-3">
                                    <li>"Register Now" button shows at the top right (and in the phone menu)</li>
                                    <li>Registration pop-up, homepage "Register Now" section and event-page button show</li>
                                    <li>Clicking the hero banner can open the form (Admin &rsaquo; Hero Banner)</li>
                                    <li>New registrations are accepted</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-secondary font-weight-bold">When OFF</p>
                                <ul class="small pl-3">
                                    <li>All of the above are hidden</li>
                                    <li>The /ticket page shows the "closed" message below</li>
                                    <li>Any registration still submitted is rejected</li>
                                    <li>Existing bookings are kept</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Wording</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="registration_button_text">Top-right button text</label>
                            <input type="text" class="form-control @error('registration_button_text') is-invalid @enderror"
                                name="registration_button_text" id="registration_button_text" maxlength="40"
                                placeholder="{{ $defaultLabel }}"
                                value="{{ old('registration_button_text', $setting->registration_button_text) }}">
                            <small class="form-text text-muted">Leave empty for "{{ $defaultLabel }}".</small>
                        </div>

                        <div class="form-group">
                            <label for="registration_closed_message">Message when closed</label>
                            <textarea class="form-control @error('registration_closed_message') is-invalid @enderror"
                                name="registration_closed_message" id="registration_closed_message" rows="3" maxlength="255"
                                placeholder="{{ $defaultMessage }}">{{ old('registration_closed_message', $setting->registration_closed_message) }}</textarea>
                            <small class="form-text text-muted">Leave empty for the default message.</small>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Save</button>
                        <a href="{{ route('home') }}" target="_blank" class="btn btn-outline-secondary btn-block">
                            View homepage
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop
