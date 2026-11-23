{{-- Ticketing > Coupons for the chosen event. Needs: $event, $passTypes, $coupons. --}}
@php $couponOpen = $event->date && $event->date->gte(today()); @endphp
<div class="card card-info card-outline" id="coupons">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-ticket-alt mr-2"></i>Coupons <small class="text-muted ml-1">codes customers type on the registration form, e.g. for ALITA members</small></h3>
    </div>
    <div class="card-body">
        @if ($coupons->isNotEmpty())
            <div class="table-responsive mb-3">
                <table class="table table-sm table-hover mb-0">
                    <thead class="thead-light"><tr><th>Coupon</th><th>Discount</th><th>Pass</th><th>Codes</th><th>Used (paid)</th><th>Awaiting payment</th><th>Valid</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @foreach ($coupons as $c)
                            @php $cs = \App\Support\Coupons::stats($c); $why = $c->closedReason(); @endphp
                            <tr>
                                <td><a href="{{ route('coupons.show', $c) }}" class="font-weight-bold">{{ $c->name }}</a></td>
                                <td class="text-nowrap">{{ $c->label() }}</td>
                                <td class="small">{{ $c->passType?->name ?? 'Any pass' }}<div class="text-muted">{{ $c->max_passes }} per use</div></td>
                                <td>{{ number_format($cs['codes']) }}</td>
                                <td>{{ number_format($cs['used']) }}</td>
                                <td>{{ number_format($cs['reserved']) }}</td>
                                <td class="small text-nowrap">{{ $c->valid_from?->format('j M') ?? '' }}{{ $c->valid_from || $c->valid_until ? ' – ' : 'Always' }}{{ $c->valid_until?->format('j M Y') ?? '' }}</td>
                                <td>@if ($why)<span class="badge badge-secondary" title="{{ $why }}">Off</span>@else<span class="badge badge-success">On</span>@endif</td>
                                <td class="text-right"><a href="{{ route('coupons.show', $c) }}" class="btn btn-outline-primary btn-xs">Manage codes</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if (! $couponOpen)
            <div class="alert alert-light border mb-0">This event is over – pick an upcoming event above to make a coupon.</div>
        @elseif ($passTypes->isEmpty())
            <div class="alert alert-light border mb-0">Add a pass above first.</div>
        @else
            <form action="{{ route('coupons.store') }}" method="post" class="border rounded p-3 bg-light">
                @csrf
                <input type="hidden" name="service_id" value="{{ $event->id }}">
                <h6 class="mb-3">New coupon</h6>
                <div class="form-row">
                    <div class="col-md-3 form-group">
                        <label class="small mb-0" for="cp_name">Name <span class="text-danger">*</span></label>
                        <input type="text" name="coupon_name" id="cp_name" maxlength="120" class="form-control form-control-sm" placeholder="e.g. ALITA members – 20% off" value="{{ old('coupon_name') }}">
                    </div>
                    <div class="col-md-2 form-group">
                        <label class="small mb-0" for="cp_value">Discount <span class="text-danger">*</span></label>
                        <div class="input-group input-group-sm">
                            <input type="number" name="discount_value" id="cp_value" min="0.01" step="0.01" class="form-control" placeholder="20" value="{{ old('discount_value') }}">
                            <div class="input-group-append">
                                <select name="discount_type" class="custom-select custom-select-sm" aria-label="Discount type" style="max-width: 110px">
                                    <option value="percent" @selected(old('discount_type', 'percent') === 'percent')>% off</option>
                                    <option value="amount" @selected(old('discount_type') === 'amount')>₹ off</option>
                                    <option value="price" @selected(old('discount_type') === 'price')>= ₹ price</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 form-group">
                        <label class="small mb-0" for="cp_pass">Pass</label>
                        <select name="coupon_pass" id="cp_pass" class="form-control form-control-sm">
                            <option value="">Any pass</option>
                            @foreach ($passTypes as $p)<option value="{{ $p->id }}" @selected((string) old('coupon_pass') === (string) $p->id)>{{ $p->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-md-1 form-group">
                        <label class="small mb-0" for="cp_max" title="How many passes one use of a code covers">Passes / use</label>
                        <input type="number" name="max_passes" id="cp_max" min="1" max="20" class="form-control form-control-sm" value="{{ old('max_passes', 1) }}">
                    </div>
                    <div class="col-md-2 form-group">
                        <label class="small mb-0" for="cp_from">From <small class="text-muted">(optional)</small></label>
                        <input type="date" name="valid_from" id="cp_from" class="form-control form-control-sm" value="{{ old('valid_from') }}">
                    </div>
                    <div class="col-md-2 form-group">
                        <label class="small mb-0" for="cp_until">Until <small class="text-muted">(optional)</small></label>
                        <input type="date" name="valid_until" id="cp_until" class="form-control form-control-sm" max="{{ $event->date->toDateString() }}" value="{{ old('valid_until') }}">
                    </div>
                </div>
                <div class="d-flex flex-wrap align-items-center">
                    <div class="custom-control custom-checkbox mr-4">
                        <input type="hidden" name="once_per_email" value="0">
                        <input type="checkbox" class="custom-control-input" id="cp_once" name="once_per_email" value="1" @checked(old('once_per_email', '1') === '1')>
                        <label class="custom-control-label small" for="cp_once">One use per email address (across all codes of this coupon)</label>
                    </div>
                    <button type="submit" class="btn btn-info btn-sm ml-auto">Create coupon</button>
                </div>
            </form>
        @endif
    </div>
</div>
