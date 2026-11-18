{{-- Ticketing > Special price offers for the chosen event. Needs: $event, $passTypes, $offers. --}}
@php
    $offerOpen = $event->date && $event->date->gte(today());
    $money = fn ($v) => \App\Support\Pricing::money((float) $v);
@endphp
<div class="card card-warning card-outline" id="offers">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-tag mr-2"></i>Special price offers <small class="text-muted ml-1">a pass at a special price for a list of people – personal payment links</small></h3>
    </div>
    <div class="card-body">
        @if ($offers->isNotEmpty())
            <div class="table-responsive mb-3">
                <table class="table table-sm table-hover mb-0">
                    <thead class="thead-light"><tr><th>Offer</th><th>Pass</th><th>Price</th><th>Link opened</th><th>Paid</th><th>Last day</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @foreach ($offers as $o)
                            @php $st = \App\Support\SpecialOffers::stats($o); $reason = $o->closedReason(); @endphp
                            <tr>
                                <td><a href="{{ route('offers.show', $o) }}" class="font-weight-bold">{{ $o->title }}</a></td>
                                <td class="small">{{ $o->passType?->name ?? '–' }}</td>
                                <td class="text-nowrap">{{ $money($o->unit_price) }} <small class="text-muted">{{ $o->charge_tax ? '+ GST' : 'no GST' }}</small></td>
                                <td>{{ number_format($st['views']) }}</td>
                                <td>{{ number_format($st['paid_people']) }} <small class="text-muted">({{ $st['paid_passes'] }} {{ \Illuminate\Support\Str::plural('pass', $st['paid_passes']) }})</small></td>
                                <td class="small text-nowrap">{{ $o->expires_on?->format('j M Y') ?? '–' }}</td>
                                <td>@if ($reason)<span class="badge badge-secondary" title="{{ $reason }}">Closed</span>@else<span class="badge badge-success">Open</span>@endif</td>
                                <td class="text-right text-nowrap">
                                    <button type="button" class="btn btn-outline-success btn-xs" title="Copy the offer link for your campaign"
                                        onclick="navigator.clipboard && navigator.clipboard.writeText('{{ $o->sharedLink() }}'); this.textContent = 'Copied';">Copy link</button>
                                    <a href="{{ route('offers.show', $o) }}" class="btn btn-outline-primary btn-xs">Manage</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if (! $offerOpen)
            <div class="alert alert-light border mb-0">This event is over – pick an upcoming event above to make an offer.</div>
        @elseif ($passTypes->isEmpty())
            <div class="alert alert-light border mb-0">Add a pass above first – an offer is a special price for one of the passes.</div>
        @else
            <form action="{{ route('offers.store') }}" method="post" class="border rounded p-3 bg-light">
                @csrf
                <input type="hidden" name="service_id" value="{{ $event->id }}">
                <h6 class="mb-3">New offer</h6>
                <div class="form-row">
                    <div class="col-md-3 form-group">
                        <label class="small mb-0" for="of_title">Name <span class="text-danger">*</span> <small class="text-muted">shown to people</small></label>
                        <input type="text" name="title" id="of_title" maxlength="120" class="form-control form-control-sm" placeholder="e.g. Delegate Pass – special price" value="{{ old('title') }}">
                    </div>
                    <div class="col-md-2 form-group">
                        <label class="small mb-0" for="of_pass">Pass <span class="text-danger">*</span></label>
                        <select name="pass_type_id" id="of_pass" class="form-control form-control-sm">
                            @foreach ($passTypes as $p)
                                <option value="{{ $p->id }}" @selected((string) old('pass_type_id') === (string) $p->id)>{{ $p->name }} ({{ $money($p->priceOn()) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <label class="small mb-0" for="of_price">Offer price per pass <span class="text-danger">*</span></label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend"><span class="input-group-text">₹</span></div>
                            <input type="number" name="unit_price" id="of_price" min="1" step="0.01" class="form-control" placeholder="7999" value="{{ old('unit_price') }}">
                        </div>
                    </div>
                    <div class="col-md-2 form-group">
                        <label class="small mb-0" for="of_max">Passes per person</label>
                        <input type="number" name="max_per_person" id="of_max" min="1" max="20" class="form-control form-control-sm" value="{{ old('max_per_person', 5) }}">
                    </div>
                    <div class="col-md-2 form-group">
                        <label class="small mb-0" for="of_exp">Last day <small class="text-muted">(optional)</small></label>
                        <input type="date" name="expires_on" id="of_exp" class="form-control form-control-sm" min="{{ today()->toDateString() }}" max="{{ $event->date->toDateString() }}" value="{{ old('expires_on') }}">
                    </div>
                    <div class="col-md-1 form-group d-flex align-items-end">
                        <button type="submit" class="btn btn-warning btn-sm btn-block">Create</button>
                    </div>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="hidden" name="charge_tax" value="0">
                    <input type="checkbox" class="custom-control-input" id="of_tax" name="charge_tax" value="1" @checked(old('charge_tax') === '1')>
                    <label class="custom-control-label small" for="of_tax">Add GST as set below in Tax &amp; payment (unticked: the offer price is the total, no GST)</label>
                </div>
            </form>
        @endif
    </div>
</div>
