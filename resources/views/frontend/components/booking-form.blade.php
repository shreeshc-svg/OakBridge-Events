{{-- Registration / ticket form. Prices come from Admin > Ticketing. --}}
@if (!$registrationOpen)
    <div class="alert alert-info my-2">{{ $registrationClosedMessage }}</div>
@else
    @php
        $obEvents = collect($upcomingEvents ?? []);
        $obPricing = $obEvents->mapWithKeys(fn($e) => [$e->title => \App\Support\Pricing::payload($e)]);
        $obHasPricing = $obPricing->contains(fn($p) => count($p['passes']) > 0);
    @endphp

    <form id="registerForm" class="ob-register-form" action="{{ route('book.ticket') }}" method="post"
        data-pricing="{{ json_encode($obPricing) }}">
        @csrf

        @if ($errors->has('registration_closed'))
            <div class="alert alert-danger my-2">
                {{ $errors->first('registration_closed') }}
            </div>
        @endif

        <div class="mb-3">
            <label>Your Name<span class="text-danger">*</span></label>
            <input type="text" placeholder="Full name" name="name" class="form-control" value="{{ old('name') }}">
            @error('name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label>Email<span class="text-danger">*</span></label>
            <input type="email" placeholder="Email id" name="email" class="form-control" value="{{ old('email') }}">
            @error('email')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label>Phone<span class="text-danger">*</span></label> <small>only 10 Digits no space</small>
            <input type="tel" placeholder="Mobile no." name="phone" class="form-control" value="{{ old('phone') }}">
            @error('phone')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label>Organization <small class="text-muted">(optional)</small></label>
                    <input type="text" placeholder="Company name" name="company" class="form-control"
                        value="{{ old('company') }}">
                    @error('company')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label>Designation <small class="text-muted">(optional)</small></label>
                    <input type="text" placeholder="Designation" name="designation" class="form-control"
                        value="{{ old('designation') }}">
                    @error('designation')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label>Event</label>
            <select name="event" class="form-select w-100 border py-2 rounded ob-event" required>
                <option value="">Select an Event</option>
                @foreach ($obEvents as $obEvent)
                    <option value="{{ $obEvent->title }}" {{ old('event') == $obEvent->title ? 'selected' : '' }}>
                        {{ $obEvent->title }}
                    </option>
                @endforeach
            </select>
            @error('event')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        @if ($obHasPricing)
            {{-- passes: shown only for events that have prices --}}
            <div class="ob-tickets d-none">
                <div class="row">
                    <div class="col-md-7">
                        <div class="mb-3">
                            <label>Pass type<span class="text-danger">*</span></label>
                            <select name="pass_type_id" class="form-select w-100 border py-2 rounded ob-pass"></select>
                            <small class="text-muted ob-pass-note"></small>
                            @error('pass_type_id')
                                <span class="text-danger d-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="mb-3">
                            <label>Number of passes</label>
                            <select name="quantity" class="form-select w-100 border py-2 rounded ob-quantity"></select>
                            @error('quantity')
                                <span class="text-danger d-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="ob-attendees mb-3"></div>
                @foreach ($errors->get('attendees.*') as $attendeeError)
                    <div class="text-danger mb-2">{{ $attendeeError[0] }}</div>
                @endforeach

                <div class="ob-summary mb-3 p-3 rounded" style="background:#f8f9fa;border:1px solid #e9ecef"></div>

                {{-- billing details: every paid order gets a GST invoice --}}
                <div class="ob-billing mb-3 p-3 rounded" style="border:1px solid #e9ecef">
                    <div class="mb-2"><strong>Billing details</strong> <small class="text-muted">for your GST invoice</small></div>
                    <div class="mb-3">
                        <label>Billing address<span class="text-danger">*</span></label>
                        <input type="text" name="billing_address" class="form-control ob-bill-req" maxlength="500"
                            placeholder="Office / flat, building, street, city" value="{{ old('billing_address') }}">
                        @error('billing_address')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-7">
                            <div class="mb-3">
                                <label>State<span class="text-danger">*</span></label>
                                <select name="billing_state" class="form-select w-100 border py-2 rounded ob-bill-req">
                                    <option value="">Select state</option>
                                    @foreach (\App\Support\GstStates::names() as $obState)
                                        <option value="{{ $obState }}" @selected(old('billing_state') === $obState)>{{ $obState }}</option>
                                    @endforeach
                                </select>
                                @error('billing_state')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label>PIN code<span class="text-danger">*</span></label>
                                <input type="text" name="billing_pin" class="form-control ob-bill-req" maxlength="6"
                                    inputmode="numeric" pattern="[0-9]{6}" value="{{ old('billing_pin') }}">
                                @error('billing_pin')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label>Company GSTIN <small>(optional – to claim input tax credit)</small></label>
                        <input type="text" name="buyer_gstin" class="form-control text-uppercase" maxlength="15"
                            placeholder="27AAAAA0000A1Z5" value="{{ old('buyer_gstin') }}">
                        @error('buyer_gstin')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    @php $obGstRate = rtrim(rtrim(number_format((float) ($setting->tax_percent ?? 18), 2), '0'), '.'); @endphp
                    <small class="text-muted d-block mt-2">Your tax invoice, showing {{ $obGstRate }}% GST, is emailed once your payment goes through.</small>
                </div>
            </div>
        @endif

        <div class="mb-3">
            <button id="submitBtn" type="submit" class="btn btn-primary ob-submit">Register Now</button>
        </div>
    </form>

    @once
        <script>
            (function() {
                var OLD = {
                    pass: @json(old('pass_type_id')),
                    quantity: @json(old('quantity')),
                    attendees: @json(old('attendees', []))
                };

                function money(value) {
                    var rounded = Math.round(value * 100) / 100;
                    var text = rounded.toFixed(rounded % 1 === 0 ? 0 : 2);
                    var parts = text.split('.');
                    var whole = parts[0];
                    if (whole.length > 3) {
                        var last3 = whole.slice(-3);
                        whole = whole.slice(0, -3).replace(/\B(?=(\d{2})+(?!\d))/g, ',') + ',' + last3;
                    }
                    return '₹' + whole + (parts[1] ? '.' + parts[1] : '');
                }

                // same maths as App\Support\Pricing - the server always recalculates before saving
                function bundleTotal(pass, quantity) {
                    var bundles = pass.bundles || {};
                    var quantities = Object.keys(bundles).map(Number).sort(function(a, b) { return a - b; });
                    if (!quantities.length) {
                        return null;
                    }
                    if (bundles[quantity] !== undefined) {
                        return bundles[quantity];
                    }
                    var largest = quantities[quantities.length - 1];
                    if (quantity > largest) {
                        return Math.round((bundles[largest] + (quantity - largest) * pass.extra) * 100) / 100;
                    }
                    return null;
                }

                function quote(pricing, pass, quantity) {
                    var unit = pass.price;
                    var subtotal = Math.round(unit * quantity * 100) / 100;
                    var net, discount, label = null, isBundle = false;

                    var bundle = bundleTotal(pass, quantity);
                    if (bundle !== null) {
                        isBundle = true;
                        net = Math.round(bundle * 100) / 100;
                        discount = Math.round(Math.max(subtotal - net, 0) * 100) / 100;
                        if (discount > 0) {
                            label = 'Bundle price for ' + quantity + ' passes';
                        }
                    } else {
                        var tier = null;
                        (pricing.tiers || []).forEach(function(row) {
                            if (quantity >= row.min && (!tier || row.min > tier.min)) {
                                tier = row;
                            }
                        });
                        var perPassOff = 0;
                        if (tier) {
                            perPassOff = tier.type === 'percent' ? unit * (tier.value / 100) : tier.value;
                            perPassOff = Math.min(Math.max(Math.round(perPassOff * 100) / 100, 0), unit);
                            label = tier.label;
                        }
                        discount = Math.round(perPassOff * quantity * 100) / 100;
                        net = Math.round((subtotal - discount) * 100) / 100;
                    }

                    var tax = 0,
                        total = net;
                    if (pricing.taxPercent > 0) {
                        if (pricing.taxIncluded) {
                            tax = Math.round((net - net / (1 + pricing.taxPercent / 100)) * 100) / 100;
                        } else {
                            tax = Math.round(net * pricing.taxPercent / 100 * 100) / 100;
                            total = Math.round((net + tax) * 100) / 100;
                        }
                    }
                    return {
                        unit: unit, subtotal: subtotal, discount: discount, label: label,
                        isBundle: isBundle, net: net, tax: tax, total: total,
                        perPass: quantity ? Math.round(total / quantity * 100) / 100 : 0
                    };
                }

                // "2 more passes and each costs less" - only while the next step is cheaper per pass
                function nextStepHint(pricing, pass, quantity) {
                    var current = quote(pricing, pass, quantity);
                    for (var next = quantity + 1; next <= Math.min(quantity + 4, pricing.max); next++) {
                        var ahead = quote(pricing, pass, next);
                        if (ahead.perPass < current.perPass - 1) {
                            var more = next - quantity;
                            return 'Add ' + more + ' more pass' + (more > 1 ? 'es' : '') + ' and each pass costs ' +
                                money(Math.round(ahead.perPass)) + ' instead of ' + money(Math.round(current.perPass)) + '.';
                        }
                    }
                    return '';
                }

                function setup(form) {
                    var pricing = {};
                    try {
                        pricing = JSON.parse(form.dataset.pricing || '{}');
                    } catch (e) {
                        pricing = {};
                    }
                    var eventSelect = form.querySelector('.ob-event');
                    var box = form.querySelector('.ob-tickets');
                    if (!eventSelect || !box) {
                        return;
                    }
                    var passSelect = form.querySelector('.ob-pass');
                    var passNote = form.querySelector('.ob-pass-note');
                    var quantitySelect = form.querySelector('.ob-quantity');
                    var attendees = form.querySelector('.ob-attendees');
                    var summary = form.querySelector('.ob-summary');
                    var restore = { pass: OLD.pass, quantity: OLD.quantity, attendees: OLD.attendees };

                    function current() {
                        return pricing[eventSelect.value] || null;
                    }

                    function fillPasses() {
                        var data = current();
                        var hasPasses = data && data.passes.length > 0;
                        box.classList.toggle('d-none', !hasPasses);
                        box.querySelectorAll('.ob-bill-req').forEach(function(field) { field.required = hasPasses; });
                        passSelect.disabled = !hasPasses;
                        quantitySelect.disabled = !hasPasses;
                        if (!hasPasses) {
                            attendees.innerHTML = '';
                            summary.innerHTML = '';
                            return;
                        }

                        passSelect.innerHTML = '';
                        data.passes.forEach(function(pass) {
                            var option = document.createElement('option');
                            option.value = pass.id;
                            option.textContent = pass.name + ' – ' + money(pass.price) + (pass.early ? ' (early bird)' : '');
                            passSelect.appendChild(option);
                        });
                        if (restore.pass) {
                            passSelect.value = restore.pass;
                            restore.pass = null;
                        }

                        quantitySelect.innerHTML = '';
                        for (var i = 1; i <= data.max; i++) {
                            var option = document.createElement('option');
                            option.value = i;
                            option.textContent = i + (i === 1 ? ' pass' : ' passes');
                            quantitySelect.appendChild(option);
                        }
                        if (restore.quantity) {
                            quantitySelect.value = restore.quantity;
                            restore.quantity = null;
                        }
                        render();
                    }

                    function render() {
                        var data = current();
                        if (!data || !data.passes.length) {
                            return;
                        }
                        var pass = data.passes.filter(function(p) {
                            return String(p.id) === String(passSelect.value);
                        })[0] || data.passes[0];
                        var quantity = parseInt(quantitySelect.value || '1', 10);
                        var result = quote(data, pass, quantity);

                        passNote.textContent = pass.early ? 'Early bird price, while the offer lasts.' : '';
                        buildAttendees(quantity);

                        var lines = '<div class="d-flex justify-content-between"><span>' + pass.name + ' × ' + quantity +
                            '</span><span>' + money(result.subtotal) + '</span></div>';
                        if (result.discount > 0) {
                            lines += '<div class="d-flex justify-content-between text-success"><span>' + (result.label || 'Discount') +
                                '</span><span>– ' + money(result.discount) + '</span></div>';
                        }
                        if (result.tax > 0 && !data.taxIncluded) {
                            lines += '<div class="d-flex justify-content-between"><span>' + data.taxLabel +
                                '</span><span>' + money(result.tax) + '</span></div>';
                        }
                        lines += '<div class="d-flex justify-content-between mt-2 pt-2 border-top"><strong>Total</strong><strong>' +
                            money(result.total) + '</strong></div>';
                        if (result.tax > 0 && data.taxIncluded) {
                            lines += '<div class="small text-muted">Includes ' + data.taxLabel + ' of ' + money(result.tax) + '.</div>';
                        }
                        if (quantity > 1) {
                            lines += '<div class="small text-muted">' + money(Math.round(result.perPass)) + ' per pass</div>';
                        }
                        var hint = nextStepHint(data, pass, quantity);
                        if (hint) {
                            lines += '<div class="small text-muted mt-1">' + hint + '</div>';
                        }
                        summary.innerHTML = lines;
                    }

                    function buildAttendees(quantity) {
                        var wanted = quantity - 1;
                        if (attendees.querySelectorAll('.ob-attendee').length === wanted) {
                            return;
                        }
                        var typed = {};
                        attendees.querySelectorAll('.ob-attendee').forEach(function(row) {
                            typed[row.dataset.number] = {
                                name: row.querySelector('input[data-field=name]').value,
                                email: row.querySelector('input[data-field=email]').value
                            };
                        });
                        attendees.innerHTML = wanted > 0 ? '<p class="mb-2"><strong>Who are the other passes for?</strong></p>' : '';
                        for (var number = 2; number <= quantity; number++) {
                            var saved = typed[number] || (restore.attendees || {})[number] || { name: '', email: '' };
                            var row = document.createElement('div');
                            row.className = 'ob-attendee row';
                            row.dataset.number = number;
                            row.innerHTML =
                                '<div class="col-md-6 mb-2"><input type="text" class="form-control" data-field="name"' +
                                ' name="attendees[' + number + '][name]" placeholder="Pass ' + number + ' – full name"></div>' +
                                '<div class="col-md-6 mb-2"><input type="email" class="form-control" data-field="email"' +
                                ' name="attendees[' + number + '][email]" placeholder="Pass ' + number + ' – email"></div>';
                            attendees.appendChild(row);
                            row.querySelector('input[data-field=name]').value = saved.name || '';
                            row.querySelector('input[data-field=email]').value = saved.email || '';
                        }
                        restore.attendees = null;
                    }

                    eventSelect.addEventListener('change', fillPasses);
                    passSelect.addEventListener('change', render);
                    quantitySelect.addEventListener('change', render);
                    fillPasses();

                    form.addEventListener('submit', function() {
                        var button = form.querySelector('.ob-submit');
                        if (button) {
                            button.disabled = true;
                            button.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i> Registering...';
                        }
                    });
                }

                function init() {
                    document.querySelectorAll('form.ob-register-form').forEach(setup);
                }

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', init);
                } else {
                    init();
                }
            })();
        </script>
    @endonce
@endif
