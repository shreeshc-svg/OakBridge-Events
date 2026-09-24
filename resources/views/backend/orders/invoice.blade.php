{{-- GST invoice for this order: billing details, issue, download, resend. --}}
@php
    $invoice = $order->invoice;
    $blocked = \App\Support\InvoiceIssuer::blockedReason($order);
    $states = \App\Support\GstStates::names();
@endphp

<div class="card card-primary card-outline">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">Tax invoice</h3>
        @if ($invoice)
            <span class="badge badge-success">{{ $invoice->number }}</span>
        @endif
    </div>

    <div class="card-body">
        @if ($errors->has('invoice'))
            <div class="alert alert-warning py-2">{{ $errors->first('invoice') }}</div>
        @endif

        @if ($invoice)
            <dl class="row small mb-2">
                <dt class="col-5">Issued</dt><dd class="col-7">{{ $invoice->issued_at->format('j M Y, H:i') }}</dd>
                <dt class="col-5">Amount</dt><dd class="col-7">INR {{ \App\Support\InvoicePdf::amount($invoice->snapshot['total']) }}</dd>
                <dt class="col-5">GST</dt>
                <dd class="col-7">
                    @forelse ($invoice->snapshot['taxes'] as $t)
                        {{ $t['label'] }} {{ \App\Support\InvoicePdf::amount($t['amount']) }}@if (!$loop->last), @endif
                    @empty
                        none
                    @endforelse
                </dd>
                <dt class="col-5">Emailed</dt>
                <dd class="col-7">
                    @if ($invoice->sent_count)
                        {{ $invoice->sent_count }}× – last {{ $invoice->last_sent_at?->format('j M Y, H:i') }}
                    @else
                        <span class="text-danger">not yet</span>
                    @endif
                </dd>
            </dl>

            <div class="d-flex">
                <a href="{{ route('orders.invoice.download', $order) }}" class="btn btn-outline-primary btn-sm flex-fill mr-2">
                    <i class="fas fa-fw fa-download"></i> Download PDF
                </a>
                <form action="{{ route('orders.invoice.send', $order) }}" method="post" class="flex-fill"
                    onsubmit="return confirm('Email invoice {{ $invoice->number }} to {{ $invoice->snapshot['buyer']['email'] }}?');">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm btn-block">
                        <i class="fas fa-fw fa-paper-plane"></i> {{ $invoice->sent_count ? 'Email again' : 'Email to buyer' }}
                    </button>
                </form>
            </div>
            <p class="text-muted small mt-2 mb-0">
                Issued invoices are fixed. To correct one, issue a credit note in your accounts.
            </p>
        @else
            <form action="{{ route('orders.billing', $order) }}" method="post" class="mb-3">
                @csrf
                <div class="form-group mb-2">
                    <label class="small mb-1" for="buyer_gstin">Buyer GSTIN <span class="text-muted">(for business buyers)</span></label>
                    <input type="text" class="form-control form-control-sm text-uppercase @error('buyer_gstin') is-invalid @enderror"
                        id="buyer_gstin" name="buyer_gstin" maxlength="15" value="{{ old('buyer_gstin', $order->buyer_gstin) }}">
                    @error('buyer_gstin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group mb-2">
                    <label class="small mb-1" for="billing_address">Billing address</label>
                    <textarea class="form-control form-control-sm" id="billing_address" name="billing_address" rows="2"
                        maxlength="500">{{ old('billing_address', $order->billing_address) }}</textarea>
                </div>
                <div class="form-row">
                    <div class="form-group col-7 mb-2">
                        <label class="small mb-1" for="billing_state">State</label>
                        <select class="form-control form-control-sm" id="billing_state" name="billing_state">
                            <option value="">–</option>
                            @foreach ($states as $state)
                                <option value="{{ $state }}" @selected(old('billing_state', $order->billing_state) === $state)>{{ $state }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-5 mb-2">
                        <label class="small mb-1" for="billing_pin">PIN</label>
                        <input type="text" class="form-control form-control-sm @error('billing_pin') is-invalid @enderror"
                            id="billing_pin" name="billing_pin" maxlength="6" value="{{ old('billing_pin', $order->billing_pin) }}">
                    </div>
                </div>
                <button type="submit" class="btn btn-outline-secondary btn-sm btn-block">Save billing details</button>
            </form>

            <form action="{{ route('orders.invoice.issue', $order) }}" method="post"
                onsubmit="return confirm('Issue the invoice and email it to {{ $order->buyer_email }}? Its number is final once issued.');">
                @csrf
                <button type="submit" class="btn btn-primary btn-block" {{ $blocked ? 'disabled' : '' }}>
                    <i class="fas fa-fw fa-file-invoice"></i> Issue invoice and email it
                </button>
            </form>
            @if ($blocked)
                <p class="text-muted small mt-2 mb-0">
                    {{ $blocked }}
                    @if (\App\Support\InvoiceIssuer::missingSellerDetails())
                        <a href="{{ route('invoices.settings') }}">Invoice details</a>
                    @endif
                </p>
            @else
                <p class="text-muted small mt-2 mb-0">Check the billing details first – the invoice cannot be edited after it is issued.</p>
            @endif
        @endif
    </div>
</div>
