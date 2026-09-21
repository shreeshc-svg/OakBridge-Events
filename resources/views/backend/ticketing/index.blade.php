@extends('adminlte::page')

@section('title', 'Ticketing')

@section('content_header')
    <h1>Ticketing <small class="text-muted">prices &amp; discounts</small></h1>
@stop

@section('content')
    @include('backend.partials.alerts')

    @php $money = fn ($amount) => \App\Support\Pricing::money((float) $amount); @endphp

    <p class="text-muted">
        Add a pass with a price to start charging for an event. An event with no pass stays free to register.
        Bulk discounts apply to every event. Payment is confirmed by you in
        <a href="{{ route('orders.index') }}">Orders</a> until the payment gateway is switched on.
    </p>

    @if (!$event)
        <div class="alert alert-warning">Add an event first in Events, then set its prices here.</div>
    @else
        <form method="get" class="form-inline mb-3">
            <label class="mr-2" for="eventPicker"><strong>Event</strong></label>
            <select name="event" id="eventPicker" class="form-control" onchange="this.form.submit()">
                @foreach ($events as $option)
                    <option value="{{ $option->id }}" @selected($option->id === $event->id)>
                        {{ $option->title }}{{ $option->date ? ' – ' . $option->date->format('j M Y') : '' }}
                    </option>
                @endforeach
            </select>
        </form>

        <div class="row">
            <div class="col-lg-7">
                <div class="card card-primary card-outline">
                    <div class="card-header"><h3 class="card-title">Passes for {{ $event->title }}</h3></div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0 ticket-table">
                                <thead>
                                    <tr>
                                        <th style="min-width:150px">Pass name</th>
                                        <th style="width:120px">Price</th>
                                        <th style="width:120px">Early bird</th>
                                        <th style="width:150px">Early bird until</th>
                                        <th style="width:70px">Order</th>
                                        <th class="text-center" style="width:70px">On sale</th>
                                        <th style="width:150px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($passTypes as $pass)
                                        @php $formId = 'pass-' . $pass->id; @endphp
                                        <tr class="{{ $pass->is_active ? '' : 'is-off' }}">
                                            <td>
                                                <input form="{{ $formId }}" type="text" name="name" value="{{ $pass->name }}"
                                                    class="form-control form-control-sm" maxlength="60" required>
                                                <input form="{{ $formId }}" type="text" name="description" value="{{ $pass->description }}"
                                                    class="form-control form-control-sm mt-1" maxlength="255" placeholder="Short note (optional)">
                                            </td>
                                            <td>
                                                <input form="{{ $formId }}" type="number" step="0.01" min="0" name="price"
                                                    value="{{ (float) $pass->price }}" class="form-control form-control-sm" required>
                                            </td>
                                            <td>
                                                <input form="{{ $formId }}" type="number" step="0.01" min="0" name="early_price"
                                                    value="{{ $pass->early_price !== null ? (float) $pass->early_price : '' }}"
                                                    class="form-control form-control-sm" placeholder="–">
                                            </td>
                                            <td>
                                                <input form="{{ $formId }}" type="date" name="early_until"
                                                    value="{{ $pass->early_until?->format('Y-m-d') }}" class="form-control form-control-sm">
                                            </td>
                                            <td>
                                                <input form="{{ $formId }}" type="number" name="sort_order" value="{{ $pass->sort_order }}"
                                                    min="0" max="999" class="form-control form-control-sm">
                                            </td>
                                            <td class="text-center">
                                                <input form="{{ $formId }}" type="checkbox" name="is_active" value="1"
                                                    @checked($pass->is_active) aria-label="On sale">
                                            </td>
                                            <td class="text-nowrap">
                                                <form id="{{ $formId }}" action="{{ route('ticketing.pass.update', $pass) }}" method="post" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="service_id" value="{{ $event->id }}">
                                                    <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                                </form>
                                                <form action="{{ route('ticketing.pass.destroy', $pass) }}" method="post" class="d-inline"
                                                    onsubmit="return confirm('Delete the pass &quot;{{ addslashes($pass->name) }}&quot;? Orders already placed keep their prices.');">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-muted text-center py-3">
                                            No passes yet – this event is free to register.
                                        </td></tr>
                                    @endforelse

                                    <tr class="table-success">
                                        <td>
                                            <input form="addPass" type="text" name="name" class="form-control form-control-sm"
                                                placeholder="e.g. Delegate" maxlength="60" required value="{{ old('name') }}">
                                        </td>
                                        <td>
                                            <input form="addPass" type="number" step="0.01" min="0" name="price"
                                                class="form-control form-control-sm" placeholder="0" required value="{{ old('price') }}">
                                        </td>
                                        <td>
                                            <input form="addPass" type="number" step="0.01" min="0" name="early_price"
                                                class="form-control form-control-sm" placeholder="–" value="{{ old('early_price') }}">
                                        </td>
                                        <td>
                                            <input form="addPass" type="date" name="early_until" class="form-control form-control-sm"
                                                value="{{ old('early_until') }}">
                                        </td>
                                        <td></td>
                                        <td class="text-center">
                                            <input form="addPass" type="checkbox" name="is_active" value="1" checked>
                                        </td>
                                        <td>
                                            <form id="addPass" action="{{ route('ticketing.pass.store') }}" method="post">
                                                @csrf
                                                <input type="hidden" name="service_id" value="{{ $event->id }}">
                                                <button type="submit" class="btn btn-sm btn-success">+ Add pass</button>
                                            </form>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card card-primary card-outline">
                    <div class="card-header"><h3 class="card-title">What a buyer pays</h3></div>
                    <div class="card-body p-0">
                        @if (!count($examples))
                            <p class="text-muted p-3 mb-0">Add a pass to see the totals here.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm mb-0 text-right">
                                    <thead>
                                        <tr>
                                            <th class="text-left">Passes</th>
                                            @foreach (array_keys(reset($examples)) as $quantity)
                                                <th>{{ $quantity }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($examples as $passName => $line)
                                            <tr>
                                                <th class="text-left">{{ $passName }}</th>
                                                @foreach ($line as $quantity => $quote)
                                                    <td @class(['text-success' => $quote['discount'] > 0])>
                                                        {{ $money($quote['total']) }}
                                                        @if ($quote['discount'] > 0)
                                                            <br><small>{{ $money($quote['per_pass']) }} each</small>
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <p class="text-muted small p-2 mb-0">Totals include any discount and tax below.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card card-primary card-outline">
                    <div class="card-header"><h3 class="card-title">Bulk discounts (all events)</h3></div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th style="width:110px">From</th>
                                        <th>Discount</th>
                                        <th style="width:110px"></th>
                                    </tr>
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
                                        <tr><td colspan="3" class="text-muted text-center py-3">No bulk discounts yet.</td></tr>
                                    @endforelse

                                    <tr class="table-success">
                                        <td class="text-nowrap">
                                            <input form="addTier" type="number" name="min_quantity" min="2" max="50"
                                                class="form-control form-control-sm d-inline-block" style="width:70px"
                                                placeholder="2" required> +
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
                        <p class="text-muted small p-2 mb-0">
                            The best matching row applies: with rows at 2 and 5, someone buying 6 passes gets the 5+ discount.
                        </p>
                    </div>
                </div>

                <div class="card card-primary card-outline">
                    <div class="card-header"><h3 class="card-title">Tax &amp; payment</h3></div>
                    <form action="{{ route('ticketing.settings') }}" method="post">
                        @csrf
                        <input type="hidden" name="event_id" value="{{ $event->id }}">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group col-6">
                                    <label for="tax_percent">Tax percentage</label>
                                    <input type="number" step="0.01" min="0" max="99.99" class="form-control" id="tax_percent"
                                        name="tax_percent" value="{{ old('tax_percent', $setting->tax_percent !== null ? (float) $setting->tax_percent : '') }}"
                                        placeholder="e.g. 18">
                                    <small class="form-text text-muted">Leave empty for no tax.</small>
                                </div>
                                <div class="form-group col-6">
                                    <label for="tax_label">Shown as</label>
                                    <input type="text" class="form-control" id="tax_label" name="tax_label" maxlength="30"
                                        value="{{ old('tax_label', $setting->tax_label) }}" placeholder="GST">
                                </div>
                            </div>
                            <div class="custom-control custom-switch mb-3">
                                <input type="checkbox" class="custom-control-input" id="prices_include_tax" name="prices_include_tax"
                                    value="1" @checked(old('prices_include_tax', $setting->prices_include_tax ?? true))>
                                <label class="custom-control-label" for="prices_include_tax">Pass prices already include the tax</label>
                            </div>
                            <div class="form-group">
                                <label for="max_passes_per_order">Most passes in one order</label>
                                <input type="number" min="1" max="{{ \App\Support\Pricing::MAX_PASSES }}" class="form-control"
                                    id="max_passes_per_order" name="max_passes_per_order"
                                    value="{{ old('max_passes_per_order', $setting->max_passes_per_order ?: 10) }}">
                            </div>
                            <div class="form-group">
                                <label for="payment_instructions">How to pay (shown in the email)</label>
                                <textarea class="form-control" id="payment_instructions" name="payment_instructions" rows="4"
                                    placeholder="Bank name, account number, IFSC, UPI id…">{{ old('payment_instructions', $setting->payment_instructions) }}</textarea>
                            </div>
                            <div class="form-group mb-0">
                                <label for="invoice_note">Note under the amount (optional)</label>
                                <input type="text" class="form-control" id="invoice_note" name="invoice_note" maxlength="255"
                                    value="{{ old('invoice_note', $setting->invoice_note) }}" placeholder="e.g. GSTIN, cancellation policy">
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary btn-block">Save settings</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@stop

@section('css')
    <style>
        .ticket-table td { vertical-align: middle; }
        .ticket-table tr.is-off input[type=text], .ticket-table tr.is-off input[type=number] { color: #999; background: #f4f4f4; }
    </style>
@stop
