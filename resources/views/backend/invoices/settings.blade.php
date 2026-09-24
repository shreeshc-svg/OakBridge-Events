@extends('adminlte::page')

@section('title', 'Invoice Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1>Invoice Details</h1>
        <div>
            <a href="{{ route('invoices.preview') }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-fw fa-file-pdf"></i> Preview a sample invoice
            </a>
        </div>
    </div>
@stop

@section('content')
    @php
        $prefixLocked = $issuedThisYear > 0;
        $prefix = old('invoice_prefix', $setting->invoice_prefix ?: 'OBE');
        $nextNumber = $prefix . '/' . $fy . '/' . str_pad((string) ($issuedThisYear + 1), 4, '0', STR_PAD_LEFT);
    @endphp

    @include('backend.partials.alerts')

    @if ($errors->has('pdf'))
        <div class="alert alert-warning">{{ $errors->first('pdf') }}</div>
    @endif

    @if ($missing)
        <div class="alert alert-danger">
            <i class="fas fa-fw fa-pause-circle"></i>
            <strong>Invoices are on hold.</strong> Paid orders are not invoiced until you add:
            {{ implode(', ', $missing) }}. Anything paid in the meantime can be invoiced from the Orders list afterwards.
        </div>
    @else
        <div class="alert alert-success">
            <i class="fas fa-fw fa-check-circle"></i>
            <strong>Ready.</strong> The next invoice will be <strong>{{ $nextNumber }}</strong>.
            {{ $setting->invoice_auto_send ? 'It is emailed to the buyer the moment an order is paid.' : 'Automatic emailing is off – send invoices from each order.' }}
        </div>
    @endif

    @if ($eventsWithoutPlace->count())
        <div class="alert alert-warning">
            <i class="fas fa-fw fa-map-marker-alt"></i>
            <strong>Place of supply not set</strong> for {{ $eventsWithoutPlace->implode(', ') }} – these are invoiced as
            IGST and the invoice leaves the place of supply blank. Set it in
            <a href="{{ route('schedules.index') }}">Events › Schedules</a>.
        </div>
    @endif

    <form action="{{ route('invoices.settings.save') }}" method="post">
        @csrf
        <div class="row">
            <div class="col-lg-7">
                <div class="card card-primary card-outline">
                    <div class="card-header"><h3 class="card-title">Your company, as printed on the invoice</h3></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="seller_name">Registered company name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('seller_name') is-invalid @enderror" id="seller_name"
                                name="seller_name" value="{{ old('seller_name', $setting->seller_name) }}" maxlength="191"
                                placeholder="Oakbridge Publishing Pvt. Ltd.">
                        </div>
                        <div class="form-group">
                            <label for="seller_address">Registered address <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="seller_address" name="seller_address" rows="4" maxlength="500"
                                placeholder="934, 9th Floor, Tower B3,&#10;Spaze iTech Park, Sector 49,&#10;Gurgaon 122018">{{ old('seller_address', $setting->seller_address) }}</textarea>
                            <small class="form-text text-muted">One line per line, as it should appear.</small>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="seller_gstin">GSTIN <span class="text-danger">*</span></label>
                                <input type="text" class="form-control text-uppercase @error('seller_gstin') is-invalid @enderror"
                                    id="seller_gstin" name="seller_gstin" maxlength="15"
                                    value="{{ old('seller_gstin', $setting->seller_gstin) }}" placeholder="06AACCO5406D1ZW">
                                @error('seller_gstin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="seller_state">State <span class="text-danger">*</span></label>
                                <select class="form-control @error('seller_state') is-invalid @enderror" id="seller_state" name="seller_state">
                                    <option value="">Choose…</option>
                                    @foreach ($states as $state)
                                        <option value="{{ $state }}" @selected(old('seller_state', $setting->seller_state) === $state)>{{ $state }}</option>
                                    @endforeach
                                </select>
                                @error('seller_state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6 mb-md-0">
                                <label for="seller_phone">Contact numbers</label>
                                <input type="text" class="form-control" id="seller_phone" name="seller_phone" maxlength="60"
                                    value="{{ old('seller_phone', $setting->seller_phone) }}" placeholder="01244305970, 8800337299">
                            </div>
                            <div class="form-group col-md-6 mb-0">
                                <label for="seller_email">Accounts email</label>
                                <input type="email" class="form-control @error('seller_email') is-invalid @enderror" id="seller_email"
                                    name="seller_email" value="{{ old('seller_email', $setting->seller_email) }}" placeholder="fpa@oakbridge.in">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-primary card-outline">
                    <div class="card-header"><h3 class="card-title">Bank details <span class="text-muted small">(optional)</span></h3></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="bank_name">Bank name and account type</label>
                            <input type="text" class="form-control" id="bank_name" name="bank_name" maxlength="191"
                                value="{{ old('bank_name', $setting->bank_name) }}" placeholder="HDFC BANK - Current Account">
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-5 mb-0">
                                <label for="bank_account">Account number</label>
                                <input type="text" class="form-control" id="bank_account" name="bank_account" maxlength="40"
                                    value="{{ old('bank_account', $setting->bank_account) }}">
                            </div>
                            <div class="form-group col-md-7 mb-0">
                                <label for="bank_branch_ifsc">Branch &amp; IFS code</label>
                                <input type="text" class="form-control" id="bank_branch_ifsc" name="bank_branch_ifsc" maxlength="191"
                                    value="{{ old('bank_branch_ifsc', $setting->bank_branch_ifsc) }}">
                            </div>
                        </div>
                        <small class="form-text text-muted">Leave empty and the bank box is left off the invoice.</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card card-primary card-outline">
                    <div class="card-header"><h3 class="card-title">Numbering and sending</h3></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="invoice_prefix">Invoice number prefix</label>
                            <input type="text" class="form-control text-uppercase @error('invoice_prefix') is-invalid @enderror"
                                id="invoice_prefix" name="invoice_prefix" maxlength="3" value="{{ $prefix }}"
                                {{ $prefixLocked ? 'readonly' : '' }}>
                            @error('invoice_prefix')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="form-text text-muted">
                                @if ($prefixLocked)
                                    Fixed for {{ $fy }} – {{ $issuedThisYear }} invoice(s) already use it. It can change from 1 April.
                                @else
                                    Up to 3 characters. Numbers run {{ $prefix }}/{{ $fy }}/0001, 0002… and restart each 1 April.
                                    Keep it different from the book store's OAK series.
                                @endif
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="invoice_sac">SAC code</label>
                            <input type="text" class="form-control @error('invoice_sac') is-invalid @enderror" id="invoice_sac"
                                name="invoice_sac" maxlength="8" value="{{ old('invoice_sac', $setting->invoice_sac) }}">
                            @error('invoice_sac')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="form-text text-muted">The service code for event passes. Confirm it with your CA.</small>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input type="hidden" name="invoice_auto_send" value="0">
                            <input type="checkbox" class="custom-control-input" id="invoice_auto_send" name="invoice_auto_send"
                                value="1" {{ old('invoice_auto_send', $setting->invoice_auto_send ?? 1) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="invoice_auto_send">
                                <strong>Email the invoice as soon as an order is paid</strong>
                            </label>
                            <small class="form-text text-muted">Off: invoices are still numbered on payment, and you send them from the order.</small>
                        </div>

                        <div class="form-group">
                            <label for="seller_pan">Company's PAN</label>
                            <input type="text" class="form-control text-uppercase @error('seller_pan') is-invalid @enderror"
                                id="seller_pan" name="seller_pan" maxlength="10"
                                value="{{ old('seller_pan', $setting->seller_pan) }}"
                                placeholder="{{ $setting->seller_gstin ? substr($setting->seller_gstin, 2, 10) : 'AACCO5406D' }}">
                            @error('seller_pan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="form-text text-muted">Printed above the declaration. It is characters 3 to 12 of your GSTIN.</small>
                        </div>

                        <div class="form-group mb-0">
                            <label for="invoice_declaration">Declaration</label>
                            <textarea class="form-control" id="invoice_declaration" name="invoice_declaration" rows="3" maxlength="500"
                                placeholder="{{ \App\Support\InvoiceIssuer::DEFAULT_DECLARATION }}">{{ old('invoice_declaration', $setting->invoice_declaration) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="card card-outline card-secondary">
                    <div class="card-header"><h3 class="card-title">How GST is split</h3></div>
                    <div class="card-body small text-muted">
                        Each event has a <strong>place of supply</strong> (Events › Schedules). Same state as your GSTIN:
                        CGST + SGST, half each. Any other state, or not set: IGST in full. Confirm the rule for your
                        events with your CA.
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block mb-4">Save invoice details</button>
            </div>
        </div>
    </form>
@stop
