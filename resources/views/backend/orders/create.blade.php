@extends('adminlte::page')

@section('title', 'New registration')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1>New registration</h1>
        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary btn-sm">&larr; All orders</a>
    </div>
@stop

@section('content')
    @include('backend.partials.alerts')

    @if ($events->isEmpty())
        <div class="alert alert-warning">
            There is no upcoming event with passes. Publish an event and add its passes in
            <a href="{{ route('ticketing.index') }}">Ticketing</a> first.
        </div>
    @else
        <p class="text-muted">
            Register someone yourself – same details as the website form – at the normal price or a special price just for them.
            It appears in Orders and Booking like any other registration.
        </p>

        <form action="{{ route('orders.store') }}" method="post" id="adminOrder" data-pricing="{{ json_encode($pricing) }}">
            @csrf
            <div class="row">
                <div class="col-lg-7">
                    <div class="card card-primary card-outline">
                        <div class="card-header"><h3 class="card-title">Person</h3></div>
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" maxlength="100" required value="{{ old('name') }}">
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" maxlength="100" required value="{{ old('email') }}">
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="phone">Phone <span class="text-danger">*</span> <small class="text-muted">10 digits</small></label>
                                    <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" maxlength="10" inputmode="numeric" required value="{{ old('phone') }}">
                                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="company">Organization <small class="text-muted">(optional)</small></label>
                                    <input type="text" name="company" id="company" class="form-control" maxlength="100" value="{{ old('company') }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="designation">Designation <small class="text-muted">(optional)</small></label>
                                    <input type="text" name="designation" id="designation" class="form-control" maxlength="100" value="{{ old('designation') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card card-primary card-outline">
                        <div class="card-header"><h3 class="card-title">Pass</h3></div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="event">Event <span class="text-danger">*</span></label>
                                <select name="event" id="event" class="form-control @error('event') is-invalid @enderror" required>
                                    @foreach ($events as $event)
                                        <option value="{{ $event->title }}" @selected(old('event', $events->first()->title) === $event->title)>
                                            {{ $event->title }} – {{ $event->date->format('j M Y') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('event')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-8">
                                    <label for="pass_type_id">Pass type <span class="text-danger">*</span></label>
                                    <select name="pass_type_id" id="pass_type_id" class="form-control @error('pass_type_id') is-invalid @enderror" required></select>
                                    @error('pass_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="quantity">Number of passes</label>
                                    <select name="quantity" id="quantity" class="form-control"></select>
                                </div>
                            </div>
                            <div id="attendees"></div>
                            @if ($errors->has('attendees.*'))
                                <div class="text-danger small">{{ collect($errors->get('attendees.*'))->flatten()->first() }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="card card-primary card-outline">
                        <div class="card-header"><h3 class="card-title">Billing <small class="text-muted">for the GST invoice</small></h3></div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="billing_address">Billing address <span class="text-danger">*</span></label>
                                <textarea name="billing_address" id="billing_address" rows="2" maxlength="500" required
                                    class="form-control @error('billing_address') is-invalid @enderror">{{ old('billing_address') }}</textarea>
                                @error('billing_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-5">
                                    <label for="billing_state">State <span class="text-danger">*</span></label>
                                    <select name="billing_state" id="billing_state" class="form-control @error('billing_state') is-invalid @enderror">
                                        <option value="">Select state</option>
                                        @foreach (\App\Support\GstStates::names() as $state)
                                            <option value="{{ $state }}" @selected(old('billing_state') === $state)>{{ $state }}</option>
                                        @endforeach
                                    </select>
                                    @error('billing_state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="billing_pin">PIN <span class="text-danger">*</span></label>
                                    <input type="text" name="billing_pin" id="billing_pin" maxlength="6" inputmode="numeric" required
                                        class="form-control @error('billing_pin') is-invalid @enderror" value="{{ old('billing_pin') }}">
                                    @error('billing_pin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="buyer_gstin">GSTIN <small class="text-muted">(optional)</small></label>
                                    <input type="text" name="buyer_gstin" id="buyer_gstin" maxlength="15"
                                        class="form-control text-uppercase @error('buyer_gstin') is-invalid @enderror" value="{{ old('buyer_gstin') }}">
                                    @error('buyer_gstin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card card-success card-outline sticky-lg-top" style="top: 1rem">
                        <div class="card-header"><h3 class="card-title">Price and payment</h3></div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="unit_price">Price per pass (₹)</label>
                                <input type="number" name="unit_price" id="unit_price" min="0" step="0.01"
                                    class="form-control form-control-lg @error('unit_price') is-invalid @enderror" value="{{ old('unit_price') }}">
                                @error('unit_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="form-text text-muted" id="priceHint"></small>
                            </div>

                            <table class="table table-sm mb-3" id="summary">
                                <tr><td id="sumLine"></td><td class="text-right" id="sumNet"></td></tr>
                                <tr id="sumTaxRow"><td id="sumTaxLabel"></td><td class="text-right" id="sumTax"></td></tr>
                                <tr><th>Total</th><th class="text-right" id="sumTotal"></th></tr>
                            </table>

                            <div id="paymentBlock">
                                <label class="d-block">Payment</label>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="pay_link" name="payment" value="link" @checked(old('payment', 'link') === 'link')>
                                    <label class="custom-control-label" for="pay_link">They will pay – send the payment link</label>
                                    <small class="d-block text-muted">Passes and the GST invoice are issued when they pay. Payment reminders apply.</small>
                                </div>
                                <div class="custom-control custom-radio mt-2">
                                    <input type="radio" class="custom-control-input" id="pay_paid" name="payment" value="paid" @checked(old('payment') === 'paid')>
                                    <label class="custom-control-label" for="pay_paid">Already paid (bank transfer, UPI, cheque…)</label>
                                </div>
                                <div class="form-row mt-2" id="paidFields">
                                    <div class="form-group col-6 mb-2">
                                        <input type="text" name="paid_method" class="form-control form-control-sm @error('paid_method') is-invalid @enderror"
                                            maxlength="40" placeholder="Paid by, e.g. UPI" value="{{ old('paid_method') }}">
                                        @error('paid_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="form-group col-6 mb-2">
                                        <input type="text" name="paid_reference" class="form-control form-control-sm" maxlength="191"
                                            placeholder="Reference / UTR" value="{{ old('paid_reference') }}">
                                    </div>
                                </div>
                            </div>
                            <p class="text-success small mb-2 d-none" id="freeNote">
                                <i class="fas fa-gift"></i> Price ₹0 – a complimentary pass: issued straight away, no payment or invoice.
                            </p>

                            <div class="custom-control custom-checkbox mt-3">
                                <input type="hidden" name="notify" value="0">
                                <input type="checkbox" class="custom-control-input" id="notify" name="notify" value="1" @checked(old('notify', '1') === '1')>
                                <label class="custom-control-label" for="notify" id="notifyLabel">Email them</label>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-success btn-block btn-lg" id="createBtn">Create registration</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @endif
@stop

@section('js')
    @if ($events->isNotEmpty())
        <script>
            (function () {
                var form = document.getElementById('adminOrder');
                var pricing = JSON.parse(form.dataset.pricing || '{}');
                var OLD = { pass: @json(old('pass_type_id')), qty: @json(old('quantity')), attendees: @json(old('attendees', [])) };
                var el = function (id) { return document.getElementById(id); };
                var eventSel = el('event'), passSel = el('pass_type_id'), qtySel = el('quantity'), price = el('unit_price');

                function money(v) {
                    var r = Math.round(v * 100) / 100, t = r.toFixed(r % 1 === 0 ? 0 : 2), p = t.split('.'), w = p[0];
                    if (w.length > 3) { w = w.slice(0, -3).replace(/\B(?=(\d{2})+(?!\d))/g, ',') + ',' + w.slice(-3); }
                    return '₹' + w + (p[1] ? '.' + p[1] : '');
                }
                function current() { return pricing[eventSel.value] || { passes: [], max: 10, taxPercent: 0, taxIncluded: true, taxLabel: 'Tax' }; }
                function pass() { var id = parseInt(passSel.value, 10); return current().passes.filter(function (p) { return p.id === id; })[0]; }

                function fillPasses(keep) {
                    var info = current();
                    passSel.innerHTML = info.passes.map(function (p) {
                        return '<option value="' + p.id + '">' + p.name + ' – ' + money(p.price) + (p.early ? ' (early bird)' : '') + '</option>';
                    }).join('');
                    if (keep && OLD.pass) { passSel.value = OLD.pass; }
                    qtySel.innerHTML = '';
                    for (var n = 1; n <= (info.max || 10); n++) { qtySel.insertAdjacentHTML('beforeend', '<option value="' + n + '">' + n + ' pass' + (n > 1 ? 'es' : '') + '</option>'); }
                    if (keep && OLD.qty) { qtySel.value = OLD.qty; }
                }

                function attendees() {
                    var box = el('attendees'), qty = parseInt(qtySel.value, 10) || 1, html = '';
                    for (var n = 2; n <= qty; n++) {
                        var a = OLD.attendees[n] || {};
                        html += '<div class="form-row"><div class="form-group col-md-6 mb-2"><label class="small mb-0">Pass ' + n + ' – name</label>'
                            + '<input type="text" class="form-control form-control-sm" name="attendees[' + n + '][name]" maxlength="100" required value="' + (a.name || '').replace(/"/g, '&quot;') + '"></div>'
                            + '<div class="form-group col-md-6 mb-2"><label class="small mb-0">Pass ' + n + ' – email</label>'
                            + '<input type="email" class="form-control form-control-sm" name="attendees[' + n + '][email]" maxlength="100" required value="' + (a.email || '').replace(/"/g, '&quot;') + '"></div></div>';
                    }
                    box.innerHTML = html ? '<p class="small text-muted mb-1">Pass 1 is for the person above. Each other pass needs its holder\'s name and email.</p>' + html : '';
                }

                function summary() {
                    var p = pass(), info = current(), qty = parseInt(qtySel.value, 10) || 1;
                    if (!p) { return; }
                    price.placeholder = p.price;
                    var typed = price.value.trim() !== '';
                    var unit = typed ? Math.max(0, parseFloat(price.value) || 0) : p.price;
                    var listUnit = p.price;
                    var net = unit * qty, tax = 0, total = net, rate = info.taxPercent || 0;
                    if (rate > 0) {
                        if (info.taxIncluded) { tax = net - net / (1 + rate / 100); } else { tax = net * rate / 100; total = net + tax; }
                    }
                    el('sumLine').textContent = p.name + ' × ' + qty + ' @ ' + money(unit);
                    el('sumNet').textContent = money(net);
                    el('sumTaxRow').style.display = rate > 0 ? '' : 'none';
                    el('sumTaxLabel').textContent = (info.taxLabel || 'Tax') + ' ' + rate + '%' + (info.taxIncluded ? ' (included)' : '');
                    el('sumTax').textContent = money(tax);
                    el('sumTotal').textContent = money(total);
                    var special = typed && Math.abs(unit - listUnit) > 0.004;
                    el('priceHint').innerHTML = 'Current price ' + money(listUnit) + (p.early ? ' (early bird)' : '') + ' per pass, '
                        + (rate > 0 ? (info.taxIncluded ? 'GST included' : 'plus ' + rate + '% GST') : 'no tax') + '.'
                        + (special ? ' <strong class="text-primary">Special price for this person only.</strong> <a href="#" id="resetPrice">Use the current price</a>'
                            : ' Leave empty for the website price' + (qty > 1 ? ' (any group/bundle discount is applied when saved)' : '') + ', or type a special price.');
                    var reset = el('resetPrice');
                    if (reset) { reset.addEventListener('click', function (e) { e.preventDefault(); price.value = ''; summary(); }); }

                    var free = total <= 0;
                    el('freeNote').classList.toggle('d-none', !free);
                    el('paymentBlock').style.opacity = free ? .4 : 1;
                    el('paymentBlock').querySelectorAll('input').forEach(function (i) { i.disabled = free; });
                    el('paidFields').style.display = (!free && el('pay_paid').checked) ? '' : 'none';
                    var paidNow = free || el('pay_paid').checked;
                    el('notifyLabel').textContent = paidNow ? 'Email them their pass(es) now' : 'Email them the payment link now';
                    el('createBtn').textContent = free ? 'Issue complimentary pass' + (qty > 1 ? 'es' : '')
                        : (el('pay_paid').checked ? 'Create and issue pass' + (qty > 1 ? 'es' : '') : 'Create and send payment link');
                }

                eventSel.addEventListener('change', function () { fillPasses(false); attendees(); summary(); });
                passSel.addEventListener('change', summary);
                qtySel.addEventListener('change', function () { attendees(); summary(); });
                price.addEventListener('input', summary);
                form.querySelectorAll('input[name=payment]').forEach(function (r) { r.addEventListener('change', summary); });
                form.addEventListener('submit', function () { el('createBtn').disabled = true; el('createBtn').textContent = 'Saving…'; });

                fillPasses(true); attendees(); summary();
            })();
        </script>
    @endif
@stop
