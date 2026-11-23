@extends('adminlte::page')

@section('title', $coupon->name)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1>{{ $coupon->name }} <small class="text-muted">{{ $coupon->service->title }}</small></h1>
        <a href="{{ route('ticketing.index', ['event' => $coupon->service_id]) }}#coupons" class="btn btn-outline-secondary btn-sm">&larr; Ticketing</a>
    </div>
@stop

@section('content')
    @include('backend.partials.alerts')
    @php
        $money = fn ($v) => \App\Support\Pricing::money((float) $v);
        $why = $coupon->closedReason();
        $examplePass = $coupon->passType ?? $passTypes->first();
        $exampleUnit = $examplePass ? $examplePass->priceOn() : null;
    @endphp

    <div class="row">
        @foreach ([
            ['Codes', number_format($stats['codes']), 'fas fa-barcode', 'secondary'],
            ['Used (paid)', number_format($stats['used']) . ' · ' . $stats['passes'] . ' ' . \Illuminate\Support\Str::plural('pass', $stats['passes']), 'fas fa-check-circle', 'success'],
            ['Awaiting payment', number_format($stats['reserved']), 'fas fa-hourglass-half', 'warning'],
            ['Discount given', $money($stats['discount']), 'fas fa-tags', 'info'],
            ['Collected', $money($stats['revenue']), 'fas fa-rupee-sign', 'primary'],
        ] as [$label, $value, $icon, $color])
            <div class="col-6 col-md">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-{{ $color }}"><i class="{{ $icon }}"></i></span>
                    <div class="info-box-content"><span class="info-box-text">{{ $label }}</span><span class="info-box-number">{{ $value }}</span></div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row">
        <div class="col-lg-5">
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title">The coupon</h3>
                    <div class="card-tools">@if ($why)<span class="badge badge-secondary">Off – {{ $why }}</span>@else<span class="badge badge-success">On – codes work</span>@endif</div>
                </div>
                <form action="{{ route('coupons.update', $coupon) }}" method="post">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label class="small mb-0" for="name">Name</label>
                            <input type="text" name="name" id="name" maxlength="120" class="form-control form-control-sm" value="{{ old('name', $coupon->name) }}">
                        </div>
                        <div class="form-row">
                            <div class="col-6 form-group">
                                <label class="small mb-0" for="discount_value">Discount</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="discount_value" id="discount_value" min="0.01" step="0.01" class="form-control" value="{{ old('discount_value', (float) $coupon->discount_value) }}">
                                    <div class="input-group-append">
                                        <select name="discount_type" class="custom-select custom-select-sm" aria-label="Discount type" style="max-width: 70px">
                                            <option value="percent" @selected(old('discount_type', $coupon->discount_type) === 'percent')>%</option>
                                            <option value="amount" @selected(old('discount_type', $coupon->discount_type) === 'amount')>₹</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 form-group">
                                <label class="small mb-0" for="pass_type_id">Pass</label>
                                <select name="pass_type_id" id="pass_type_id" class="form-control form-control-sm">
                                    <option value="">Any pass</option>
                                    @foreach ($passTypes as $p)<option value="{{ $p->id }}" @selected((string) old('pass_type_id', $coupon->pass_type_id) === (string) $p->id)>{{ $p->name }}</option>@endforeach
                                </select>
                            </div>
                            <div class="col-4 form-group">
                                <label class="small mb-0" for="max_passes">Passes per use</label>
                                <input type="number" name="max_passes" id="max_passes" min="1" max="20" class="form-control form-control-sm" value="{{ old('max_passes', $coupon->max_passes) }}">
                            </div>
                            <div class="col-4 form-group">
                                <label class="small mb-0" for="valid_from">From</label>
                                <input type="date" name="valid_from" id="valid_from" class="form-control form-control-sm" value="{{ old('valid_from', $coupon->valid_from?->toDateString()) }}">
                            </div>
                            <div class="col-4 form-group">
                                <label class="small mb-0" for="valid_until">Until</label>
                                <input type="date" name="valid_until" id="valid_until" class="form-control form-control-sm" value="{{ old('valid_until', $coupon->valid_until?->toDateString()) }}">
                            </div>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="hidden" name="once_per_email" value="0">
                            <input type="checkbox" class="custom-control-input" id="once_per_email" name="once_per_email" value="1" @checked(old('once_per_email', $coupon->once_per_email ? '1' : '0') === '1')>
                            <label class="custom-control-label small" for="once_per_email">One use per email address (across all codes)</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" @checked(old('is_active', $coupon->is_active ? '1' : '0') === '1')>
                            <label class="custom-control-label small" for="is_active">Coupon is on (untick to stop every code at once)</label>
                        </div>
                        @if ($exampleUnit)
                            @php $off = $coupon->discountOn($exampleUnit); @endphp
                            <div class="alert alert-light border small mt-3 mb-0">
                                Example: {{ $examplePass->name }} {{ $money($exampleUnit) }} → <strong>{{ $money($exampleUnit - $off) }}</strong> per pass with a code (+ GST as set in Tax &amp; payment).
                            </div>
                        @endif
                    </div>
                    <div class="card-footer"><button class="btn btn-info btn-sm">Save coupon</button></div>
                </form>
            </div>

            <div class="card card-outline card-secondary">
                <div class="card-header"><h3 class="card-title">Add a shared code <small class="text-muted">optional – one code for many people</small></h3></div>
                <form action="{{ route('coupons.shared', $coupon) }}" method="post">
                    @csrf
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-7 form-group mb-1">
                                <label class="small mb-0" for="code">Code</label>
                                <input type="text" name="code" id="code" maxlength="40" class="form-control form-control-sm text-uppercase" placeholder="ALITA20" value="{{ old('code') }}">
                            </div>
                            <div class="col-5 form-group mb-1">
                                <label class="small mb-0" for="max_uses">Uses in total</label>
                                <input type="number" name="max_uses" id="max_uses" min="1" class="form-control form-control-sm" placeholder="e.g. 50" value="{{ old('max_uses') }}">
                            </div>
                        </div>
                        <small class="text-muted">Anyone with this code can use it until the total is reached{{ $coupon->once_per_email ? ' – each email only once' : '' }}. For tight control, use one-time codes instead.</small>
                    </div>
                    <div class="card-footer"><button class="btn btn-outline-secondary btn-sm">Add shared code</button></div>
                </form>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card card-success card-outline">
                <div class="card-header"><h3 class="card-title">Make one-time codes <small class="text-muted">each code works once</small></h3></div>
                <form action="{{ route('coupons.generate', $coupon) }}" method="post" id="generateForm">
                    @csrf
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-md-4 form-group">
                                <label class="small mb-0" for="prefix">Start each code with</label>
                                <input type="text" name="prefix" id="prefix" maxlength="20" class="form-control form-control-sm text-uppercase" value="{{ old('prefix', 'ALITA20') }}">
                                <small class="text-muted">e.g. ALITA20-7KQ3XM2P</small>
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="small mb-0" for="count">How many</label>
                                <input type="number" name="count" id="count" min="1" max="5000" class="form-control form-control-sm" placeholder="e.g. 50" value="{{ old('count') }}">
                            </div>
                        </div>
                        <div class="form-group mb-2">
                            <label class="small mb-0" for="people">…or one code per member – paste <code>Name, Email</code>, one per line (then "How many" is not needed)</label>
                            <textarea name="people" id="people" rows="4" class="form-control form-control-sm" placeholder="Member Name, member@example.com&#10;Another Member, another@example.com">{{ old('people') }}</textarea>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="hidden" name="lock_to_email" value="0">
                            <input type="checkbox" class="custom-control-input" id="lock_to_email" name="lock_to_email" value="1" @checked(old('lock_to_email', '1') === '1')>
                            <label class="custom-control-label small" for="lock_to_email">Each member's code works only with their own email (safest – a forwarded code is useless)</label>
                        </div>
                    </div>
                    <div class="card-footer d-flex flex-wrap align-items-center">
                        <button class="btn btn-success btn-sm mr-2" id="generateBtn">Make codes</button>
                        <a href="{{ route('coupons.export', $coupon) }}" class="btn btn-outline-success btn-sm"><i class="fas fa-download"></i> Download codes (CSV)</a>
                        <small class="text-muted ml-auto">The CSV lists every code, who it is for, and who used it.</small>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- the codes --}}
    <div class="card">
        <div class="card-header">
            <form method="get" class="form-inline">
                <input type="search" name="q" value="{{ $q }}" class="form-control form-control-sm mr-2 mb-1" style="min-width: 260px" placeholder="Code, member, email or order no.…">
                <select name="show" class="form-control form-control-sm mr-2 mb-1" onchange="this.form.submit()">
                    @foreach (['all' => 'All codes', 'unused' => 'Unused', 'reserved' => 'Awaiting payment', 'used' => 'Used (paid)', 'revoked' => 'Switched off'] as $value => $label)
                        <option value="{{ $value }}" @selected($show === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <button class="btn btn-sm btn-primary mb-1">Search</button>
                <span class="ml-auto small text-muted">{{ number_format($codes->total()) }} {{ \Illuminate\Support\Str::plural('code', $codes->total()) }}</span>
            </form>
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-sm table-hover mb-0 codes-table">
                <thead class="thead-light"><tr><th>Code</th><th>For</th><th>Status</th><th>Used by</th><th></th></tr></thead>
                <tbody>
                    @forelse ($codes as $code)
                        @php
                            $status = \App\Http\Controllers\CouponController::status($code);
                            $color = str_contains($status, 'Switched off') ? 'secondary' : (str_starts_with($status, 'Used') ? 'success' : (str_contains($status, 'awaiting') ? 'warning' : ($status === 'Unused' ? 'light' : 'info')));
                        @endphp
                        <tr>
                            <td class="text-nowrap">
                                <code class="code-text" id="code{{ $code->id }}">{{ $code->code }}</code>
                                <button type="button" class="btn btn-link btn-sm p-0 ml-1 copy-code" data-target="code{{ $code->id }}" title="Copy"><i class="far fa-copy"></i></button>
                                @if ($code->max_uses > 1)<div class="small text-muted">shared · {{ $code->max_uses }} uses</div>@endif
                            </td>
                            <td class="small">
                                @if ($code->assigned_email)
                                    {{ $code->assigned_name }} <div class="text-muted">{{ $code->assigned_email }}@if ($code->lock_to_email) <i class="fas fa-lock" title="Works only with this email"></i>@endif</div>
                                @else
                                    <span class="text-muted">anyone with the code</span>
                                @endif
                            </td>
                            <td><span class="badge badge-{{ $color }} {{ $color === 'light' ? 'border' : '' }}">{{ $status }}</span></td>
                            <td class="small">
                                @foreach ($code->redemptions as $r)
                                    @php $state = $r->state(); @endphp
                                    <div class="{{ $state === 'released' ? 'text-muted' : '' }}">
                                        <strong>{{ $r->name }}</strong> {{ $r->email }}
                                        · @if ($r->order)<a href="{{ route('orders.show', $r->order_id) }}">{{ $r->order_no }}</a>@else{{ $r->order_no }}@endif
                                        · {{ $r->created_at->format('j M Y, H:i') }}
                                        · {{ ['used' => 'paid', 'reserved' => 'awaiting payment', 'released' => 'cancelled – code freed'][$state] }}
                                    </div>
                                @endforeach
                            </td>
                            <td class="text-right text-nowrap">
                                <form action="{{ route('coupons.toggle', [$coupon, $code]) }}" method="post" class="d-inline">
                                    @csrf
                                    <button class="btn btn-outline-{{ $code->is_active ? 'secondary' : 'success' }} btn-xs">{{ $code->is_active ? 'Switch off' : 'Switch on' }}</button>
                                </form>
                                @if ($code->redemptions->isEmpty())
                                    <form action="{{ route('coupons.code.destroy', [$coupon, $code]) }}" method="post" class="d-inline" onsubmit="return confirm('Delete code {{ $code->code }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-link btn-sm text-danger p-0 ml-1" title="Delete (never used)"><i class="fas fa-times"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">{{ $stats['codes'] ? 'No code matches.' : 'No codes yet – make one-time codes or add a shared code above.' }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($codes->hasPages())
            <div class="card-footer">{{ $codes->links() }}</div>
        @endif
    </div>

    @if ($stats['used'] === 0 && $stats['reserved'] === 0)
        <form action="{{ route('coupons.destroy', $coupon) }}" method="post" class="text-right" onsubmit="return confirm('Delete this coupon and all its codes?');">
            @csrf
            @method('DELETE')
            <button class="btn btn-link btn-sm text-danger">Delete this coupon</button>
        </form>
    @endif
@stop

@section('css')
    <style>.codes-table td { vertical-align: top; } .code-text { font-size: .95rem; } .btn-xs { padding: .1rem .45rem; font-size: .75rem; }</style>
@stop

@section('js')
    <script>
        document.querySelectorAll('.copy-code').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var text = document.getElementById(btn.dataset.target).textContent.trim();
                if (navigator.clipboard) { navigator.clipboard.writeText(text); }
                btn.innerHTML = '<i class="fas fa-check text-success"></i>';
                setTimeout(function () { btn.innerHTML = '<i class="far fa-copy"></i>'; }, 1200);
            });
        });
        var g = document.getElementById('generateBtn');
        g.form.addEventListener('submit', function () { g.disabled = true; g.textContent = 'Making codes…'; });
    </script>
@stop
