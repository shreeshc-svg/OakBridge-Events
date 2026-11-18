@extends('frontend.layouts.app')
@section('title', $offer->title . ' | ' . $offer->service->title)
@section('description', 'Your personal offer for ' . $offer->service->title . '.')
@section('content')
    @php
        $money = fn ($v) => \App\Support\Pricing::money((float) $v);
        $event = $offer->service;
        $passName = $offer->passType?->name ?? 'Pass';
        $canBook = ! $paid && ! $closed;
    @endphp
    <section class="ob-register">
        <div class="auto-container">
            <div class="ob-register__card ob-offer" id="offer-form">
                @if ($preview)
                    <div class="alert alert-warning mb-3"><strong>Preview</strong> – this is what {{ $invite->name }} sees. Nothing can be paid from a preview.</div>
                @endif

                <div class="ob-offer__head">
                    <div class="ob-offer__tag">Your personal offer</div>
                    <h1 class="ob-offer__title">{{ $offer->title }}</h1>
                    <p class="ob-offer__event">
                        <strong>{{ $event->title }}</strong><br>
                        {{ $event->date?->format('l, j F Y') }}@if ($event->venue) · {{ $event->venue }}@endif
                    </p>
                    <div class="ob-offer__price">
                        <span class="ob-offer__amount">{{ $money($unit) }}</span>
                        <span class="ob-offer__per">per {{ $passName }}</span>
                    </div>
                    @if ($offer->expires_on && $canBook)
                        <p class="ob-offer__expiry"><i class="far fa-clock" aria-hidden="true"></i> Offer valid until {{ $offer->expires_on->format('j F Y') }}</p>
                    @endif
                </div>

                @if ($paid)
                    <div class="alert alert-success">
                        <strong>You're registered!</strong> Payment received for order {{ $paid->order_no }} –
                        {{ $paid->quantity }} {{ \Illuminate\Support\Str::plural('pass', $paid->quantity) }}.
                        Your pass {{ $paid->quantity > 1 ? 'numbers were' : 'number was' }} emailed to {{ $invite->email }}.
                        <div class="mt-2"><a href="{{ route('order.done', $paid->order_no) }}" class="btn btn-sm btn-success">View my passes</a></div>
                    </div>
                @elseif ($closed)
                    <div class="alert alert-info">
                        {{ $closed }} For registration, please visit <a href="{{ route('register.page') }}">our registration page</a>
                        or write to us at <a href="mailto:{{ $setting->email ?? 'info@oakbridge.in' }}">{{ $setting->email ?? 'info@oakbridge.in' }}</a>.
                    </div>
                @else
                    <p class="mb-2">Dear {{ $invite->name }}, this link is just for you ({{ $invite->email }}).</p>

                    @if ($pending && ! $preview)
                        <div class="alert alert-info d-flex flex-wrap align-items-center">
                            <span class="mr-auto me-auto">You have already started – order {{ $pending->order_no }}, {{ $money($pending->total) }}.</span>
                            <a href="{{ route('order.pay', $pending->order_no) }}" class="btn btn-sm btn-primary mt-1">Continue to payment</a>
                        </div>
                        <p class="small text-muted">Or change the details below and continue – the earlier unpaid order is replaced.</p>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $message)<div>{{ $message }}</div>@endforeach
                        </div>
                    @endif

                    <form id="offerForm" class="ob-register-form" action="{{ route('offer.book', $invite->token) }}" method="post"
                        data-unit="{{ $unit }}" data-tax="{{ $taxPercent }}" data-included="{{ $taxIncluded ? 1 : 0 }}" data-pass="{{ $passName }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Your Name<span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" maxlength="100" required value="{{ old('name', $invite->name) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Email</label>
                                <input type="email" class="form-control" value="{{ $invite->email }}" readonly disabled>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label>Phone<span class="text-danger">*</span> <small>10 digits</small></label>
                                <input type="tel" name="phone" class="form-control" maxlength="10" inputmode="numeric" required
                                    value="{{ old('phone', preg_replace('/^(\+?91)(?=\d{10}$)/', '', (string) $invite->phone)) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Organization <small class="text-muted">(optional)</small></label>
                                <input type="text" name="company" class="form-control" maxlength="100" value="{{ old('company') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Designation <small class="text-muted">(optional)</small></label>
                                <input type="text" name="designation" class="form-control" maxlength="100" value="{{ old('designation') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="offerQty">Number of passes</label>
                            <select name="quantity" id="offerQty" class="form-select form-control w-auto ob-offer__qty">
                                @for ($n = 1; $n <= $max; $n++)
                                    <option value="{{ $n }}" @selected((int) old('quantity', 1) === $n)>{{ $n }} {{ \Illuminate\Support\Str::plural('pass', $n) }}</option>
                                @endfor
                            </select>
                            @if ($max > 1)<small class="text-muted d-block">Up to {{ $max }} passes at the offer price – each pass gets its own pass number.</small>@endif
                        </div>
                        <div class="ob-offer__attendees mb-2"></div>

                        <div class="ob-billing mb-3 p-3 rounded" style="border:1px solid #e9ecef">
                            <div class="mb-2"><strong>Billing details</strong> <small class="text-muted">for your invoice</small></div>
                            <div class="mb-3">
                                <label>Billing address<span class="text-danger">*</span></label>
                                <input type="text" name="billing_address" class="form-control" maxlength="500" required
                                    placeholder="Office / flat, building, street, city" value="{{ old('billing_address') }}">
                            </div>
                            <div class="row">
                                <div class="col-md-5 mb-3">
                                    <label>State<span class="text-danger">*</span></label>
                                    <select name="billing_state" class="form-select form-control" required>
                                        <option value="">Select state</option>
                                        @foreach (\App\Support\GstStates::names() as $state)
                                            <option value="{{ $state }}" @selected(old('billing_state') === $state)>{{ $state }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label>PIN code<span class="text-danger">*</span></label>
                                    <input type="text" name="billing_pin" class="form-control" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" required value="{{ old('billing_pin') }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>GSTIN <small class="text-muted">(optional)</small></label>
                                    <input type="text" name="buyer_gstin" class="form-control text-uppercase" maxlength="15" value="{{ old('buyer_gstin') }}">
                                </div>
                            </div>
                        </div>

                        <div class="ob-summary mb-3 p-3 rounded" style="background:#f8f9fa;border:1px solid #e9ecef" id="offerSummary" aria-live="polite"></div>

                        <div class="ob-cta">
                            <button type="submit" class="btn ob-submit" id="offerPay" @disabled($preview)>Continue to payment</button>
                            <p class="ob-cta__assurance"><i class="fas fa-lock" aria-hidden="true"></i> Secure payment. Your passes are emailed as soon as it goes through.</p>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </section>
@stop

@section('js')
    <style>
        .ob-offer__head { text-align: center; border-bottom: 1px solid #eee; padding-bottom: 16px; margin-bottom: 18px; }
        .ob-offer__tag { display: inline-block; background: #fdecea; color: #c0392b; font-weight: 600; font-size: 13px; letter-spacing: .5px; text-transform: uppercase; border-radius: 20px; padding: 3px 12px; }
        .ob-offer__title { font-size: 28px; margin: 10px 0 6px; }
        .ob-offer__event { color: #555; margin-bottom: 10px; }
        .ob-offer__amount { font-size: 34px; font-weight: 700; color: #1a3d6d; }
        .ob-offer__per { color: #666; }
        .ob-offer__expiry { color: #c0392b; font-weight: 600; margin: 6px 0 0; }
        @media (max-width: 767px) { .ob-offer__title { font-size: 22px; } .ob-offer__amount { font-size: 28px; } }
    </style>
    @if ($canBook)
        <script>
            (function () {
                var form = document.getElementById('offerForm');
                var unit = parseFloat(form.dataset.unit), tax = parseFloat(form.dataset.tax) || 0, included = form.dataset.included === '1';
                var qty = document.getElementById('offerQty'), box = form.querySelector('.ob-offer__attendees');
                var OLD = @json(old('attendees', []));

                function money(v) {
                    var r = Math.round(v * 100) / 100, t = r.toFixed(r % 1 === 0 ? 0 : 2), p = t.split('.'), w = p[0];
                    if (w.length > 3) { w = w.slice(0, -3).replace(/\B(?=(\d{2})+(?!\d))/g, ',') + ',' + w.slice(-3); }
                    return '₹' + w + (p[1] ? '.' + p[1] : '');
                }
                function esc(s) { return String(s || '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;'); }

                function attendees() {
                    var n = parseInt(qty.value, 10) || 1, html = '';
                    for (var i = 2; i <= n; i++) {
                        var a = OLD[i] || {};
                        var keep = box.querySelector('[name="attendees[' + i + '][name]"]');
                        var name = keep ? keep.value : (a.name || ''), email = keep ? box.querySelector('[name="attendees[' + i + '][email]"]').value : (a.email || '');
                        html += '<div class="row"><div class="col-md-6 mb-2"><label class="small mb-0">Pass ' + i + ' – name<span class="text-danger">*</span></label>'
                            + '<input type="text" class="form-control" name="attendees[' + i + '][name]" maxlength="100" required value="' + esc(name) + '"></div>'
                            + '<div class="col-md-6 mb-2"><label class="small mb-0">Pass ' + i + ' – email<span class="text-danger">*</span></label>'
                            + '<input type="email" class="form-control" name="attendees[' + i + '][email]" maxlength="100" required value="' + esc(email) + '"></div></div>';
                    }
                    box.innerHTML = html ? '<p class="small text-muted mb-1">Pass 1 is yours. Each other pass needs its holder\'s name and email (one pass per email).</p>' + html : '';
                }
                function summary() {
                    var n = parseInt(qty.value, 10) || 1, net = unit * n, t = 0, total = net;
                    if (tax > 0) { if (included) { t = net - net / (1 + tax / 100); } else { t = net * tax / 100; total = net + t; } }
                    var html = '<div class="d-flex justify-content-between"><span>' + esc(form.dataset.pass) + ' × ' + n + ' @ ' + money(unit) + '</span><span>' + money(net) + '</span></div>';
                    if (tax > 0) {
                        html += '<div class="d-flex justify-content-between text-muted"><span>GST ' + tax + '%' + (included ? ' (included)' : '') + '</span><span>' + money(t) + '</span></div>';
                    }
                    html += '<div class="d-flex justify-content-between mt-1" style="font-size:1.15em"><strong>Total</strong><strong>' + money(total) + '</strong></div>';
                    document.getElementById('offerSummary').innerHTML = html;
                    document.getElementById('offerPay').textContent = 'Pay ' + money(total);
                }
                qty.addEventListener('change', function () { attendees(); summary(); });
                form.addEventListener('submit', function () { var b = document.getElementById('offerPay'); b.disabled = true; b.textContent = 'Please wait…'; });
                attendees();
                summary();
                @if ($errors->any())
                    document.getElementById('offer-form').scrollIntoView({ block: 'start' });
                @endif
            })();
        </script>
    @endif
@stop
