@extends('adminlte::page')

@section('title', 'Ticketing')

@section('content_header')
    <h1>Ticketing <small class="text-muted">prices &amp; bundles</small></h1>
@stop

@section('content')
    @include('backend.partials.alerts')

    @php
        $money = fn ($amount) => \App\Support\Pricing::money((float) $amount);
        $max = \App\Support\Pricing::maxPasses();
    @endphp

    @if (!$event)
        <div class="alert alert-warning">Add an event first in Events, then set its prices here.</div>
    @else
        <div class="d-flex align-items-center flex-wrap mb-3">
            <form method="get" class="form-inline mr-auto">
                <label class="mr-2" for="eventPicker"><strong>Event</strong></label>
                <select name="event" id="eventPicker" class="form-control" onchange="this.form.submit()">
                    @foreach ($events as $option)
                        <option value="{{ $option->id }}" @selected($option->id === $event->id)>
                            {{ $option->title }}{{ $option->date ? ' – ' . $option->date->format('j M Y') : '' }}
                        </option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">View orders</a>
        </div>

        {{-- ---------------------------------------------------------- passes --}}
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-ticket-alt mr-2"></i>Passes for {{ $event->title }}</h3>
            </div>
            <div class="card-body">
                @forelse ($passTypes as $pass)
                    @php
                        $formId = 'pass-' . $pass->id;
                        $bundles = $pass->bundles->keyBy('quantity');
                        $early = $pass->isEarlyOn();
                    @endphp
                    <div class="pass-card mb-4 {{ $pass->is_active ? '' : 'is-off' }}">
                        <div class="form-row align-items-end">
                            <div class="col-md-3 form-group mb-2">
                                <label>Pass name</label>
                                <input form="{{ $formId }}" type="text" name="name" value="{{ $pass->name }}"
                                    class="form-control form-control-lg" maxlength="60" required>
                            </div>
                            <div class="col-md-2 form-group mb-2">
                                <label>Price (1 pass)</label>
                                <div class="input-group input-group-lg">
                                    <div class="input-group-prepend"><span class="input-group-text">₹</span></div>
                                    <input form="{{ $formId }}" type="number" step="0.01" min="0" name="price"
                                        value="{{ (float) $pass->price }}" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-2 form-group mb-2">
                                <label>Early bird price</label>
                                <div class="input-group input-group-lg">
                                    <div class="input-group-prepend"><span class="input-group-text">₹</span></div>
                                    <input form="{{ $formId }}" type="number" step="0.01" min="0" name="early_price"
                                        value="{{ $pass->early_price !== null ? (float) $pass->early_price : '' }}"
                                        class="form-control" placeholder="–">
                                </div>
                            </div>
                            <div class="col-md-2 form-group mb-2">
                                <label>Early bird until</label>
                                <input form="{{ $formId }}" type="date" name="early_until"
                                    value="{{ $pass->early_until?->format('Y-m-d') }}" class="form-control form-control-lg">
                            </div>
                            <div class="col-md-3 form-group mb-2">
                                <label class="d-block">&nbsp;</label>
                                <div class="custom-control custom-switch d-inline-block mr-3" style="padding-top:.5rem">
                                    <input form="{{ $formId }}" type="checkbox" class="custom-control-input" name="is_active"
                                        value="1" id="active-{{ $pass->id }}" @checked($pass->is_active)>
                                    <label class="custom-control-label" for="active-{{ $pass->id }}">On sale</label>
                                </div>
                                <form id="{{ $formId }}" action="{{ route('ticketing.pass.update', $pass) }}" method="post" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="service_id" value="{{ $event->id }}">
                                    <input type="hidden" name="sort_order" value="{{ $pass->sort_order }}">
                                    <input type="hidden" name="description" value="{{ $pass->description }}">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </form>
                                <form action="{{ route('ticketing.pass.destroy', $pass) }}" method="post" class="d-inline"
                                    onsubmit="return confirm('Delete the pass &quot;{{ addslashes($pass->name) }}&quot;? Orders already placed keep their prices.');">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" class="btn btn-outline-danger">Delete</button>
                                </form>
                            </div>
                        </div>

                        <p class="text-muted mb-2">
                            @if ($early)
                                <span class="badge badge-success">Early bird running</span>
                                until {{ $pass->early_until?->format('j M Y') }}.
                            @elseif ($pass->early_price !== null)
                                <span class="badge badge-secondary">Early bird ended</span>
                                list price applies.
                            @endif
                        </p>

                        {{-- ------------------------------------------ bundle grid --}}
                        <form action="{{ route('ticketing.pass.bundles', $pass) }}" method="post">
                            @csrf
                            <div class="table-responsive">
                                <table class="table table-sm bundle-table mb-2">
                                    <thead>
                                        <tr>
                                            <th style="width:110px">Passes</th>
                                            <th>Early bird total</th>
                                            <th>List price total</th>
                                            <th style="width:180px">Buyer pays now</th>
                                            <th style="width:120px">Per pass</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @for ($quantity = 1; $quantity <= $max; $quantity++)
                                            @php
                                                $row = $bundles->get($quantity);
                                                $quote = \App\Support\Pricing::quote($pass, $quantity, $event);
                                            @endphp
                                            <tr class="{{ $row ? 'has-bundle' : '' }}">
                                                <th>{{ $quantity }}</th>
                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend"><span class="input-group-text">₹</span></div>
                                                        <input type="number" step="0.01" min="0"
                                                            name="bundles[{{ $quantity }}][early_total]"
                                                            value="{{ $row && $row->early_total !== null ? (float) $row->early_total : '' }}"
                                                            class="form-control" placeholder="–">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend"><span class="input-group-text">₹</span></div>
                                                        <input type="number" step="0.01" min="0"
                                                            name="bundles[{{ $quantity }}][total]"
                                                            value="{{ $row && $row->total !== null ? (float) $row->total : '' }}"
                                                            class="form-control" placeholder="–">
                                                    </div>
                                                </td>
                                                <td class="align-middle">
                                                    <strong>{{ $money($quote['total']) }}</strong>
                                                    @if ($quote['tax'] > 0 && !$quote['tax_included'])
                                                        <small class="text-muted d-block">
                                                            {{ $money($quote['net']) }} + {{ $money($quote['tax']) }} {{ $quote['tax_label'] }}
                                                        </small>
                                                    @endif
                                                </td>
                                                <td class="align-middle text-muted">{{ $money(round($quote['per_pass'])) }}</td>
                                            </tr>
                                        @endfor
                                        <tr class="table-light">
                                            <th class="align-middle">Extra</th>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend"><span class="input-group-text">₹</span></div>
                                                    <input type="number" step="0.01" min="0" name="early_extra_price"
                                                        value="{{ $pass->early_extra_price !== null ? (float) $pass->early_extra_price : '' }}"
                                                        class="form-control" placeholder="per extra pass">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend"><span class="input-group-text">₹</span></div>
                                                    <input type="number" step="0.01" min="0" name="extra_price"
                                                        value="{{ $pass->extra_price !== null ? (float) $pass->extra_price : '' }}"
                                                        class="form-control" placeholder="per extra pass">
                                                </div>
                                            </td>
                                            <td colspan="2" class="align-middle text-muted small">
                                                Each pass beyond the last filled row costs this much.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="submit" class="btn btn-primary">Save bundle prices</button>
                            <span class="text-muted small ml-2">
                                Leave a row empty to charge the plain per-pass price for that quantity.
                            </span>
                        </form>
                    </div>
                @empty
                    <p class="text-muted">No passes yet – this event is free to register.</p>
                @endforelse

                {{-- ------------------------------------------------ add a pass --}}
                <div class="border-top pt-3">
                    <form id="addPass" action="{{ route('ticketing.pass.store') }}" method="post">
                        @csrf
                        <input type="hidden" name="service_id" value="{{ $event->id }}">
                        <div class="form-row align-items-end">
                            <div class="col-md-3 form-group mb-0">
                                <label>New pass name</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Delegate"
                                    maxlength="60" value="{{ old('name') }}" required>
                            </div>
                            <div class="col-md-2 form-group mb-0">
                                <label>Price</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">₹</span></div>
                                    <input type="number" step="0.01" min="0" name="price" class="form-control"
                                        value="{{ old('price') }}" required>
                                </div>
                            </div>
                            <div class="col-md-2 form-group mb-0">
                                <label>Early bird</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">₹</span></div>
                                    <input type="number" step="0.01" min="0" name="early_price" class="form-control"
                                        placeholder="–" value="{{ old('early_price') }}">
                                </div>
                            </div>
                            <div class="col-md-2 form-group mb-0">
                                <label>Early bird until</label>
                                <input type="date" name="early_until" class="form-control" value="{{ old('early_until') }}">
                            </div>
                            <div class="col-md-3 form-group mb-0">
                                <input type="hidden" name="is_active" value="1">
                                <button type="submit" class="btn btn-success btn-block">+ Add pass</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ------------------------------------------------- collapsed panels --}}
        <div class="card card-outline card-secondary collapsed-card">
            <div class="card-header">
                <h3 class="card-title">Percentage discounts <small class="text-muted ml-2">used only where no bundle price is set</small></h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                </div>
            </div>
            <div class="card-body" style="display:none">
                <div class="table-responsive">
                    <table class="table table-sm mb-0" style="max-width:640px">
                        <thead>
                            <tr><th style="width:120px">From</th><th>Discount</th><th style="width:130px"></th></tr>
                        </thead>
                        <tbody>
                            @forelse ($tiers as $tier)
                                @php $tierForm = 'tier-' . $tier->id; @endphp
                                <tr>
                                    <td class="text-nowrap">
                                        <input form="{{ $tierForm }}" type="number" name="min_quantity" min="2" max="50"
                                            value="{{ $tier->min_quantity }}" class="form-control form-control-sm d-inline-block"
                                            style="width:70px" required> +
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input form="{{ $tierForm }}" type="number" step="0.01" min="0" name="discount_value"
                                                value="{{ (float) $tier->discount_value }}" class="form-control" required>
                                            <select form="{{ $tierForm }}" name="discount_type" class="form-control">
                                                <option value="percent" @selected($tier->discount_type === 'percent')>% off</option>
                                                <option value="flat" @selected($tier->discount_type === 'flat')>₹ off / pass</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td class="text-nowrap">
                                        <form id="{{ $tierForm }}" action="{{ route('ticketing.tier.update', $tier) }}" method="post" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="event_id" value="{{ $event->id }}">
                                            <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                        </form>
                                        <form action="{{ route('ticketing.tier.destroy', $tier) }}" method="post" class="d-inline"
                                            onsubmit="return confirm('Remove this discount?');">
                                            @csrf
                                            @method('delete')
                                            <input type="hidden" name="event_id" value="{{ $event->id }}">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">&times;</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-muted text-center py-3">No percentage discounts.</td></tr>
                            @endforelse
                            <tr class="table-success">
                                <td class="text-nowrap">
                                    <input form="addTier" type="number" name="min_quantity" min="2" max="50"
                                        class="form-control form-control-sm d-inline-block" style="width:70px" placeholder="2" required> +
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input form="addTier" type="number" step="0.01" min="0" name="discount_value"
                                            class="form-control" placeholder="10" required>
                                        <select form="addTier" name="discount_type" class="form-control">
                                            <option value="percent">% off</option>
                                            <option value="flat">₹ off / pass</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <form id="addTier" action="{{ route('ticketing.tier.store') }}" method="post">
                                        @csrf
                                        <input type="hidden" name="event_id" value="{{ $event->id }}">
                                        <button type="submit" class="btn btn-sm btn-success">+ Add</button>
                                    </form>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card card-outline card-secondary collapsed-card">
            <div class="card-header">
                <h3 class="card-title">
                    Tax &amp; payment
                    <small class="text-muted ml-2">
                        {{ $setting->tax_percent ? rtrim(rtrim(number_format((float) $setting->tax_percent, 2), '0'), '.') . '% ' . ($setting->tax_label ?: 'tax') . ($setting->prices_include_tax ? ' included' : ' added on top') : 'no tax' }}
                        &middot; {{ \App\Support\Razorpay::enabled() ? 'Razorpay on' : 'bank transfer' }}
                    </small>
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                </div>
            </div>
            <div class="card-body" style="display:none">
                <form action="{{ route('ticketing.settings') }}" method="post">
                    @csrf
                    <input type="hidden" name="event_id" value="{{ $event->id }}">
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <label for="tax_percent">Tax percentage</label>
                            <input type="number" step="0.01" min="0" max="99.99" class="form-control" id="tax_percent"
                                name="tax_percent" value="{{ old('tax_percent', $setting->tax_percent !== null ? (float) $setting->tax_percent : '') }}"
                                placeholder="18">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="tax_label">Shown as</label>
                            <input type="text" class="form-control" id="tax_label" name="tax_label" maxlength="30"
                                value="{{ old('tax_label', $setting->tax_label) }}" placeholder="GST">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="d-block">&nbsp;</label>
                            <div class="custom-control custom-switch" style="padding-top:.5rem">
                                <input type="checkbox" class="custom-control-input" id="prices_include_tax" name="prices_include_tax"
                                    value="1" @checked(old('prices_include_tax', $setting->prices_include_tax ?? true))>
                                <label class="custom-control-label" for="prices_include_tax">Prices already include the tax</label>
                            </div>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="max_passes_per_order">Max passes / order</label>
                            <input type="number" min="1" max="{{ \App\Support\Pricing::MAX_PASSES }}" class="form-control"
                                id="max_passes_per_order" name="max_passes_per_order"
                                value="{{ old('max_passes_per_order', $setting->max_passes_per_order ?: 10) }}">
                        </div>
                    </div>
                    @php
                        $razorpayKeys = (bool) \App\Support\Razorpay::keyId();
                    @endphp
                    <div class="border rounded p-3 mb-3" style="background:#f8f9fa">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="online_payment" name="online_payment"
                                value="1" @checked(old('online_payment', $setting->online_payment)) @disabled(!$razorpayKeys)>
                            <label class="custom-control-label" for="online_payment">
                                <strong>Take payment online with Razorpay</strong>
                            </label>
                        </div>
                        <small class="form-text text-muted">
                            @if (!$razorpayKeys)
                                Add RAZORPAY_KEY and RAZORPAY_SECRET to the server's .env file first, then this can be switched on.
                            @elseif (\App\Support\Razorpay::isTestMode())
                                <span class="badge badge-warning">Test keys</span>
                                Payments will not charge real money until live keys are in .env.
                            @else
                                <span class="badge badge-success">Live keys</span>
                                Buyers go straight to checkout and their passes are issued the moment payment succeeds.
                            @endif
                            When this is off, buyers get the bank details below and you mark orders paid in Orders.
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="payment_instructions">How to pay (shown when online payment is off)</label>
                        <textarea class="form-control" id="payment_instructions" name="payment_instructions" rows="3"
                            placeholder="Bank name, account number, IFSC, UPI id…">{{ old('payment_instructions', $setting->payment_instructions) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="invoice_note">Note under the amount (optional)</label>
                        <input type="text" class="form-control" id="invoice_note" name="invoice_note" maxlength="255"
                            value="{{ old('invoice_note', $setting->invoice_note) }}" placeholder="e.g. GSTIN, cancellation policy">
                    </div>
                    <button type="submit" class="btn btn-primary">Save settings</button>
                </form>
            </div>
        </div>
    @endif
@stop

@section('css')
    <style>
        .pass-card { border: 1px solid #dee2e6; border-radius: .35rem; padding: 1rem; background: #fdfdfd; }
        .pass-card.is-off { opacity: .6; }
        .bundle-table td, .bundle-table th { vertical-align: middle; }
        .bundle-table tr.has-bundle { background: #f4f9ff; }
        .bundle-table .input-group-text { padding: .2rem .5rem; }
    </style>
@stop
