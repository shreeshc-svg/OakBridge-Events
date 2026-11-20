@extends('adminlte::page')

@section('title', 'Emails sent')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1>Emails sent <small class="text-muted">by the website</small></h1>
        <div class="small text-muted">
            {{ number_format($stats['total']) }} logged · {{ number_format($stats['today']) }} sent today
            @if ($stats['failed'])
                · <a href="{{ route('emails.index', ['status' => 'failed']) }}" class="text-danger">{{ $stats['failed'] }} not delivered to the mail server</a>
            @endif
        </div>
    </div>
@stop

@section('content')
    {{-- global search --}}
    <form method="get" action="{{ route('emails.index') }}" class="card card-body py-3 mb-3" id="emailSearch" autocomplete="off">
        <div class="position-relative">
            <div class="input-group input-group-lg">
                <div class="input-group-prepend"><span class="input-group-text bg-white"><i class="fas fa-search"></i></span></div>
                <input type="search" name="q" id="q" value="{{ $person ?: $q }}" class="form-control" autofocus
                    placeholder="Search a person: name, email, phone – or a pass / order number">
                <div class="input-group-append"><button class="btn btn-primary">Search</button></div>
            </div>
            <div id="suggest" class="list-group position-absolute w-100 shadow d-none" style="z-index: 1050; max-height: 360px; overflow-y: auto"></div>
        </div>
        <div class="form-row mt-2 small">
            <div class="col-md-3 my-1">
                <select name="type" class="form-control form-control-sm" onchange="this.form.submit()">
                    <option value="">All kinds of email</option>
                    @foreach ($types as $t)<option value="{{ $t }}" @selected(request('type') === $t)>{{ $t }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-2 my-1">
                <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                    <option value="">Sent and not sent</option>
                    <option value="failed" @selected(request('status') === 'failed')>Not sent only</option>
                </select>
            </div>
            <div class="col-md-2 my-1"><input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm" title="From" onchange="this.form.submit()"></div>
            <div class="col-md-2 my-1"><input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm" title="To" onchange="this.form.submit()"></div>
            @if ($person)<input type="hidden" name="person" value="{{ $person }}">@endif
            @if (request()->hasAny(['q', 'type', 'status', 'from', 'to', 'person']))
                <div class="col-auto my-1"><a href="{{ route('emails.index') }}" class="btn btn-link btn-sm">Clear</a></div>
            @endif
        </div>
    </form>

    {{-- one person --}}
    @if ($profile)
        <div class="card card-primary card-outline">
            <div class="card-body d-flex flex-wrap align-items-center">
                <div class="mr-4">
                    <h4 class="mb-0">{{ $profile['name'] ?: $profile['email'] }}</h4>
                    <div>{{ $profile['email'] }}@if ($profile['phone']) · {{ $profile['phone'] }}@endif</div>
                </div>
                <div class="mr-4 small">
                    <strong>{{ $profile['count'] }}</strong> {{ \Illuminate\Support\Str::plural('email', $profile['count']) }}
                    @if ($profile['first'])· {{ \Illuminate\Support\Carbon::parse($profile['first'])->format('j M Y') }} – {{ \Illuminate\Support\Carbon::parse($profile['last'])->format('j M Y') }}@endif
                </div>
                <div class="small">
                    @foreach ($profile['types'] as $type => $n)<span class="badge badge-light border mr-1">{{ $type }} × {{ $n }}</span>@endforeach
                </div>
                <a href="{{ route('emails.index') }}" class="btn btn-outline-secondary btn-sm ml-auto">&larr; Everyone</a>
            </div>
        </div>
    @endif

    {{-- matching people --}}
    @if ($people->isNotEmpty())
        <div class="card">
            <div class="card-header py-2"><strong>People</strong> <span class="text-muted small">matching “{{ $q }}” – open one to see everything they were sent</span></div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <tbody>
                        @foreach ($people as $p)
                            <tr>
                                <td><a href="{{ route('emails.index', ['person' => $p->to_email]) }}" class="font-weight-bold">{{ $p->to_name ?: $p->to_email }}</a>
                                    <div class="small text-muted">{{ $p->to_email }}@if ($p->phone) · {{ $p->phone }}@endif</div></td>
                                <td class="small">{{ $p->types }}</td>
                                <td class="small text-nowrap">{{ $p->emails }} {{ \Illuminate\Support\Str::plural('email', $p->emails) }}</td>
                                <td class="small text-nowrap text-muted">last {{ \Illuminate\Support\Carbon::parse($p->last_at)->format('j M Y, H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- the emails --}}
    <div class="card">
        <div class="card-header py-2"><strong>Emails</strong> <span class="text-muted small">{{ number_format($emails->total()) }} found – newest first</span></div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-sm table-hover mb-0 email-table">
                <thead class="thead-light"><tr><th>When</th><th>To</th><th>What</th><th>Subject</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @forelse ($emails as $e)
                        <tr>
                            <td class="small text-nowrap">{{ ($e->sent_at ?? $e->created_at)->format('j M Y, H:i') }}</td>
                            <td>
                                <a href="{{ route('emails.index', ['person' => $e->to_email]) }}">{{ $e->to_name ?: $e->to_email }}</a>
                                <div class="small text-muted">{{ $e->to_email }}@if ($e->phone) · {{ $e->phone }}@endif</div>
                                @if ($e->cc)<div class="small text-muted">copy: {{ $e->cc }}</div>@endif
                            </td>
                            <td class="small"><span class="badge badge-light border">{{ $e->type }}</span>@if ($e->refs)<div class="text-muted mt-1">{{ \Illuminate\Support\Str::limit($e->refs, 60) }}</div>@endif</td>
                            <td class="small">{{ $e->subject }}</td>
                            <td class="small text-nowrap">
                                @if ($e->status === 'sent')<span class="text-success"><i class="fas fa-check"></i> Sent</span>
                                @elseif ($e->failed())<span class="text-danger" title="The mail server did not accept it"><i class="fas fa-times"></i> Not sent</span>
                                @else<span class="text-muted">Sending…</span>@endif
                            </td>
                            <td class="text-right"><a href="{{ route('emails.show', $e->id) }}" class="btn btn-outline-primary btn-xs">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">{{ $stats['total'] ? 'No email matches.' : 'No emails logged yet – every email the website sends from now on appears here.' }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($emails->hasPages())
            <div class="card-footer">{{ $emails->links() }}</div>
        @endif
    </div>
@stop

@section('css')
    <style>.email-table td { vertical-align: top; } .btn-xs { padding: .1rem .45rem; font-size: .75rem; }</style>
@stop

@section('js')
    <script>
        (function () {
            var input = document.getElementById('q'), box = document.getElementById('suggest'), timer = null, last = '';
            function esc(s) { return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;'); }
            function hide() { box.classList.add('d-none'); box.innerHTML = ''; }
            input.addEventListener('input', function () {
                clearTimeout(timer);
                var term = input.value.trim();
                if (term.length < 2) { hide(); return; }
                timer = setTimeout(function () {
                    if (term === last) { return; }
                    last = term;
                    fetch(@json(route('emails.suggest')) + '?q=' + encodeURIComponent(term), { headers: { 'Accept': 'application/json' } })
                        .then(function (r) { return r.json(); })
                        .then(function (people) {
                            if (input.value.trim() !== term) { return; }
                            if (!people.length) { box.innerHTML = '<div class="list-group-item small text-muted">No person matches – press Enter to search subjects and pass numbers too.</div>'; box.classList.remove('d-none'); return; }
                            box.innerHTML = people.map(function (p) {
                                return '<a class="list-group-item list-group-item-action py-2" href="' + p.url + '"><strong>' + esc(p.name || p.email) + '</strong>'
                                    + ' <span class="small text-muted">' + esc(p.email) + (p.phone ? ' · ' + esc(p.phone) : '') + '</span>'
                                    + '<div class="small text-muted">' + p.emails + ' email' + (p.emails === 1 ? '' : 's') + ' – ' + esc(p.types) + '</div></a>';
                            }).join('');
                            box.classList.remove('d-none');
                        });
                }, 200);
            });
            input.addEventListener('keydown', function (e) { if (e.key === 'Escape') { hide(); } });
            document.addEventListener('click', function (e) { if (!box.contains(e.target) && e.target !== input) { hide(); } });
            // a new search replaces the person being viewed
            document.getElementById('emailSearch').addEventListener('submit', function () {
                var p = this.querySelector('input[name=person]');
                if (p && input.value.trim().toLowerCase() !== p.value) { p.remove(); }
            });
        })();
    </script>
@stop
