@extends('adminlte::page')

@section('title', $offer->title)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1>{{ $offer->title }} <small class="text-muted">{{ $offer->service->title }}</small></h1>
        <a href="{{ route('ticketing.index', ['event' => $offer->service_id]) }}#offers" class="btn btn-outline-secondary btn-sm">&larr; Ticketing</a>
    </div>
@stop

@section('content')
    @include('backend.partials.alerts')
    @php
        $money = fn ($v) => \App\Support\Pricing::money((float) $v);
        $reason = $offer->closedReason();
        $labels = ['paid' => ['Paid', 'success'], 'checkout' => ['Started payment', 'warning'], 'opened' => ['Opened link', 'info'], 'sent' => ['Not opened', 'light']];
    @endphp

    @if ($problems = session('upload_problems'))
        <div class="alert alert-warning small">
            <strong>Skipped lines</strong> (fix and upload them again – people already added are not duplicated):
            <ul class="mb-0">@foreach ($problems as $p)<li>{{ $p }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- numbers --}}
    <div class="row">
        @foreach ([
            ['Offer link opened', number_format($stats['views']) . ' ' . \Illuminate\Support\Str::plural('time', $stats['views']), 'fas fa-mouse-pointer', 'info'],
            ['People who started', number_format($stats['people']), 'fas fa-users', 'secondary'],
            ['Started payment', number_format($stats['checkout']), 'fas fa-hourglass-half', 'warning'],
            ['Paid', number_format($stats['paid_people']) . ' · ' . $stats['paid_passes'] . ' ' . \Illuminate\Support\Str::plural('pass', $stats['paid_passes']), 'fas fa-check-circle', 'success'],
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

    {{-- the link for the campaign --}}
    @php $link = $offer->sharedLink(); @endphp
    <div class="card card-success card-outline" id="offerLink">
        <div class="card-header"><h3 class="card-title"><i class="fas fa-link mr-1"></i> Your offer link – paste it into your campaign</h3></div>
        <div class="card-body">
            <div class="input-group mb-2">
                <input type="text" class="form-control form-control-lg" readonly value="{{ $link }}" id="sharedLink">
                <div class="input-group-append">
                    <button type="button" class="btn btn-success copy-link" data-target="sharedLink"><i class="far fa-copy"></i> Copy</button>
                    <a href="{{ $link }}?preview=1" target="_blank" rel="noopener" class="btn btn-outline-secondary"><i class="fas fa-eye"></i> Preview</a>
                    <a href="{{ $link }}" target="_blank" rel="noopener" class="btn btn-outline-primary" title="Try it as a customer – a real payment issues real passes">Try it</a>
                </div>
            </div>
            <p class="small mb-2">
                One link for everyone: select your customer segment in your mail tool and put this link on the button.
                Each customer enters their details and pays {{ \App\Support\Pricing::money((float) $offer->unit_price) }} per pass{{ $offer->charge_tax ? ' + GST' : '' }};
                every pass gets its own pass number. Passes per email: {{ $offer->minPasses() > 1 ? $offer->minPasses() . '–' . $offer->maxPasses() . ' (checkout opens from ' . $offer->minPasses() . ')' : 'up to ' . $offer->maxPasses() }}.
            </p>
            <details class="small">
                <summary>Optional: fill in the customer's email and name for them</summary>
                <p class="mb-1 mt-2">If your mail tool has merge tags, add them to the link so the form opens pre-filled (use your tool's own tag names):</p>
                <code class="d-block p-2 bg-light border rounded">{{ $link }}?email=@{{email}}&amp;name=@{{name}}</code>
                <p class="mb-0 mt-1 text-muted">Without tags the link works the same – customers just type their email.</p>
            </details>
            @unless ($offer->open_to_anyone)
                <div class="alert alert-warning small mt-2 mb-0">"Only people on the list" is on: the link works only for emails added under <strong>Personal links</strong> below.</div>
            @endunless
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5">
            {{-- 1. the offer --}}
            <div class="card card-warning card-outline">
                <div class="card-header">
                    <h3 class="card-title">The offer</h3>
                    <div class="card-tools">@if ($reason)<span class="badge badge-secondary">Closed – {{ $reason }}</span>@else<span class="badge badge-success">Open – links work</span>@endif</div>
                </div>
                <form action="{{ route('offers.update', $offer) }}" method="post">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label class="small mb-0" for="title">Name <small class="text-muted">shown to people</small></label>
                            <input type="text" name="title" id="title" maxlength="120" class="form-control form-control-sm" value="{{ old('title', $offer->title) }}">
                        </div>
                        <div class="form-row">
                            <div class="col-6 form-group">
                                <label class="small mb-0" for="pass_type_id">Pass</label>
                                <select name="pass_type_id" id="pass_type_id" class="form-control form-control-sm">
                                    @foreach ($passTypes as $p)
                                        <option value="{{ $p->id }}" @selected((int) old('pass_type_id', $offer->pass_type_id) === $p->id)>{{ $p->name }}{{ $p->is_active ? '' : ' (off)' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 form-group">
                                <label class="small mb-0" for="unit_price">Price per pass (₹)</label>
                                <input type="number" name="unit_price" id="unit_price" min="1" step="0.01" class="form-control form-control-sm" value="{{ old('unit_price', (float) $offer->unit_price) }}">
                            </div>
                            <div class="col-3 form-group">
                                <label class="small mb-0" for="min_per_person" title="Checkout stays off until the customer chooses at least this many passes">Min passes</label>
                                <input type="number" name="min_per_person" id="min_per_person" min="1" max="20" class="form-control form-control-sm" value="{{ old('min_per_person', $offer->minPasses()) }}">
                            </div>
                            <div class="col-3 form-group">
                                <label class="small mb-0" for="max_per_person">Max passes</label>
                                <input type="number" name="max_per_person" id="max_per_person" min="1" max="20" class="form-control form-control-sm" value="{{ old('max_per_person', $offer->max_per_person) }}">
                            </div>
                            <div class="col-6 form-group">
                                <label class="small mb-0" for="expires_on">Last day</label>
                                <input type="date" name="expires_on" id="expires_on" class="form-control form-control-sm" value="{{ old('expires_on', $offer->expires_on?->toDateString()) }}">
                            </div>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="hidden" name="charge_tax" value="0">
                            <input type="checkbox" class="custom-control-input" id="charge_tax" name="charge_tax" value="1" @checked(old('charge_tax', $offer->charge_tax ? '1' : '0') === '1')>
                            <label class="custom-control-label small" for="charge_tax">Add GST (unticked: {{ $money($offer->unit_price) }} is the total)</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="hidden" name="open_to_anyone" value="0">
                            <input type="checkbox" class="custom-control-input" id="open_to_anyone" name="open_to_anyone" value="1" @checked(old('open_to_anyone', $offer->open_to_anyone ? '1' : '0') === '1')>
                            <label class="custom-control-label small" for="open_to_anyone">Anyone with the offer link can use it (untick: only emails on the list below)</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" @checked(old('is_active', $offer->is_active ? '1' : '0') === '1')>
                            <label class="custom-control-label small" for="is_active">Offer is open (untick to stop all links at once)</label>
                        </div>
                        <small class="text-muted d-block mt-2">Changes apply to everyone's link straight away. Orders already placed keep their price.</small>
                    </div>
                    <div class="card-footer d-flex">
                        <button type="submit" class="btn btn-warning btn-sm">Save offer</button>
                        <a href="{{ route('offers.preview', $offer) }}" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm ml-2"><i class="fas fa-eye"></i> Preview page</a>
                    </div>
                </form>
            </div>

            {{-- 2. test --}}
            <div class="card card-info card-outline">
                <div class="card-header"><h3 class="card-title">Personal test link</h3></div>
                <div class="card-body">
                    <p class="small text-muted">Only needed for personal links: get one for yourself and go through it like a guest. Test links are not counted or exported. (For the offer link above, just use "Try it".)</p>
                    <form action="{{ route('offers.test', $offer) }}" method="post" class="input-group input-group-sm mb-2">
                        @csrf
                        <input type="email" name="test_email" class="form-control" value="{{ old('test_email', auth()->user()?->email) }}" aria-label="Your email">
                        <div class="input-group-append"><button class="btn btn-info">Get my test link</button></div>
                    </form>
                    @foreach ($tests as $t)
                        <div class="input-group input-group-sm mb-1">
                            <div class="input-group-prepend"><span class="input-group-text">{{ $t->email }}</span></div>
                            <input type="text" class="form-control" readonly value="{{ $t->link() }}" id="t{{ $t->id }}">
                            <div class="input-group-append">
                                <a class="btn btn-outline-info" href="{{ $t->link() }}" target="_blank" rel="noopener">Open</a>
                                <button type="button" class="btn btn-outline-secondary copy-link" data-target="t{{ $t->id }}">Copy</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            {{-- 3. people --}}
            <div class="card card-primary card-outline">
                <div class="card-header"><h3 class="card-title">Optional: personal links for a list <small class="text-muted">not needed when you use the offer link</small></h3></div>
                <form action="{{ route('offers.upload', $offer) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label class="small mb-0" for="list">Paste from Excel – one person per line: <code>Name, Email, Phone</code> (phone optional; a header row is fine)</label>
                            <textarea name="list" id="list" rows="4" class="form-control form-control-sm" placeholder="Ananya Tyagi, tyagi.ananya@gmail.com, 9990406009&#10;Manan Arora	foundersdesk@juristsjunction.com"></textarea>
                        </div>
                        <div class="form-group mb-0">
                            <label class="small mb-0" for="csv">…or upload a CSV (thousands of rows are fine)</label>
                            <input type="file" name="csv" id="csv" accept=".csv,text/csv" class="form-control-file">
                        </div>
                        <small class="text-muted d-block mt-2">
                            Everyone gets their own link. Adding the same people again does not duplicate them (they keep their link).
                            People who already have a pass or a paid order for this event are left out.
                        </small>
                    </div>
                    <div class="card-footer"><button type="submit" class="btn btn-primary btn-sm" id="uploadBtn">Add to the list</button></div>
                </form>
            </div>

            {{-- 4. links for the mail tool --}}
            <div class="card card-success card-outline">
                <div class="card-header"><h3 class="card-title">Download personal links / who used the offer</h3></div>
                <div class="card-body">
                    <p class="small mb-2">CSV columns: <code>Name, Email, Phone, Offer link, Status</code>. In your mail tool, put the <strong>Offer link</strong> column in the email as a merge field (e.g. <code>@{{offer_link}}</code>).</p>
                    <a href="{{ route('offers.export', $offer) }}" class="btn btn-success btn-sm"><i class="fas fa-download"></i> Everyone ({{ number_format($stats['people']) }})</a>
                    <a href="{{ route('offers.export', [$offer, 'only' => 'unpaid']) }}" class="btn btn-outline-success btn-sm"><i class="fas fa-download"></i> Not paid yet – for a reminder ({{ number_format($stats['people'] - $stats['paid_people']) }})</a>
                </div>
            </div>
        </div>
    </div>

    {{-- the list --}}
    <div class="card">
        <div class="card-header">
            <form method="get" class="form-inline">
                <input type="search" name="q" value="{{ $q }}" class="form-control form-control-sm mr-2 mb-1" placeholder="Name or email…">
                <select name="show" class="form-control form-control-sm mr-2 mb-1" onchange="this.form.submit()">
                    @foreach (['all' => 'Everyone', 'paid' => 'Paid', 'unpaid' => 'Not paid', 'checkout' => 'Started payment', 'opened' => 'Opened link', 'not_opened' => 'Not opened'] as $value => $label)
                        <option value="{{ $value }}" @selected($filter === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <button class="btn btn-sm btn-primary mb-1">Search</button>
                <span class="ml-auto small text-muted">{{ number_format($people->total()) }} shown</span>
            </form>
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="thead-light"><tr><th>Name</th><th>Email</th><th>Phone</th><th>Status</th><th>Link</th><th></th></tr></thead>
                <tbody>
                    @forelse ($people as $person)
                        @php [$label, $color] = $labels[$statuses[$person->id] ?? ($person->clicked_at ? 'opened' : 'sent')]; @endphp
                        <tr>
                            <td>{{ $person->name }}</td>
                            <td class="small">{{ $person->email }}</td>
                            <td class="small text-nowrap">{{ $person->phone }}</td>
                            <td><span class="badge badge-{{ $color }} {{ $color === 'light' ? 'border' : '' }}">{{ $label }}</span></td>
                            <td class="small"><a href="{{ $person->link() }}?preview=1" target="_blank" rel="noopener" title="Preview their page">…/offer/{{ substr($person->token, 0, 6) }}…</a></td>
                            <td class="text-right">
                                @if (($statuses[$person->id] ?? null) !== 'paid')
                                    <form action="{{ route('offers.remove', [$offer, $person]) }}" method="post" class="d-inline"
                                        onsubmit="return confirm('Remove {{ addslashes($person->email) }} from this offer? Their link will stop working.');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-link btn-sm text-danger p-0" title="Remove from the list"><i class="fas fa-times"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">{{ $stats['people'] ? 'Nobody matches.' : 'Nobody has used the offer yet.' }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($people->hasPages())
            <div class="card-footer">{{ $people->links() }}</div>
        @endif
    </div>

    @if ($stats['people'] === 0 && $stats['paid_people'] === 0)
        <form action="{{ route('offers.destroy', $offer) }}" method="post" class="text-right" onsubmit="return confirm('Delete this offer and its list?');">
            @csrf
            @method('DELETE')
            <button class="btn btn-link btn-sm text-danger">Delete this offer</button>
        </form>
    @endif
@stop

@section('js')
    <script>
        document.querySelectorAll('.copy-link').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var input = document.getElementById(btn.dataset.target);
                input.select();
                (navigator.clipboard ? navigator.clipboard.writeText(input.value) : Promise.resolve(document.execCommand('copy'))).then(function () {
                    btn.textContent = 'Copied';
                    setTimeout(function () { btn.textContent = 'Copy'; }, 1500);
                });
            });
        });
        var up = document.getElementById('uploadBtn');
        if (up) { up.form.addEventListener('submit', function () { up.disabled = true; up.textContent = 'Adding… (large lists take a few seconds)'; }); }
    </script>
@stop
