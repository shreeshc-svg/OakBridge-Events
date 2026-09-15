@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1>Dashboard</h1>
        <small class="text-muted">Updated {{ $m['now']->format('d M Y, g:i A') }}</small>
    </div>
@stop

@php
    $k = $m['kpis'];
    $event = $m['focusEvent'];
    $fmt = fn ($n) => number_format((int) $n);
@endphp

@section('content')
    {{-- ============ Row 1: event hero + KPI tiles ============ --}}
    <div class="row">
        <div class="col-lg-5 d-flex">
            <div class="card card-primary card-outline w-100">
                <div class="card-body">
                    @if ($event)
                        <div class="dash-label">{{ $m['nextEvent'] ? 'Next event' : 'Last event' }}</div>
                        <h5 class="mb-1">{{ $event->title }}</h5>
                        <div class="text-muted small mb-3">
                            <i class="far fa-calendar"></i> {{ $event->date->format('D, d M Y') }}
                            @if ($k['daysToEvent'] !== null)
                                · <strong class="text-body">{{ $k['daysToEvent'] }} {{ \Illuminate\Support\Str::plural('day', $k['daysToEvent']) }} to go</strong>
                            @endif
                        </div>
                        <div class="dash-label">Registrations for this event</div>
                        <div class="dash-hero">{{ $fmt($k['event']) }}</div>
                        @if ($event->seat_target)
                            @php $pct = min(100, round($k['event'] / $event->seat_target * 100)); @endphp
                            <div class="dash-meter mt-2" role="img" aria-label="{{ $pct }}% of the {{ $fmt($event->seat_target) }} target">
                                <div class="dash-meter-fill {{ $pct >= 100 ? 'is-full' : '' }}" style="width: {{ $pct }}%"></div>
                            </div>
                            <div class="small text-muted mt-1">{{ $pct }}% of the {{ $fmt($event->seat_target) }} target</div>
                        @else
                            <div class="small text-muted mt-1">
                                <a href="{{ route('schedules.edit', $event) }}">Set a registration target</a> to track progress.
                            </div>
                        @endif
                        @if ($k['untagged'])
                            <div class="small text-muted mt-2">
                                <i class="fas fa-info-circle"></i>
                                {{ $fmt($k['untagged']) }} earlier {{ \Illuminate\Support\Str::plural('registration', $k['untagged']) }}
                                were made before events were recorded and aren't counted here.
                            </div>
                        @endif
                        <div class="mt-3">
                            <a href="{{ route('schedules.edit', $event) }}" class="btn btn-sm btn-outline-primary">Edit schedule</a>
                            <a href="{{ route('service.detail', $event->slug) }}" target="_blank" class="btn btn-sm btn-outline-secondary">View page</a>
                        </div>
                    @else
                        <div class="dash-label">Next event</div>
                        <p class="mb-2"><i class="fas fa-times-circle text-danger"></i> No events yet.</p>
                        <a href="{{ route('service.create') }}" class="btn btn-sm btn-primary">Add an event</a>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="row">
                @php
                    $tiles = [
                        ['Registrations today', $fmt($k['today']), null, route('booking.index')],
                        ['Last 7 days', $fmt($k['last7']), $k['delta7'], route('booking.index')],
                        ['All registrations', $fmt($k['total']), null, route('booking.index')],
                    ];
                @endphp
                @foreach ($tiles as [$label, $value, $delta, $link])
                    <div class="col-sm-6 d-flex">
                        <a href="{{ $link }}" class="card dash-tile w-100 text-reset">
                            <div class="card-body">
                                <div class="dash-label">{{ $label }}</div>
                                <div class="dash-value">{{ $value }}</div>
                                @if ($delta !== null)
                                    <div class="small {{ $delta >= 0 ? 'text-success' : 'text-danger' }}">
                                        <i class="fas {{ $delta >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                                        {{ $delta >= 0 ? '+' : '' }}{{ $delta }}% <span class="text-muted">vs previous 7 days ({{ $fmt($k['prev7']) }})</span>
                                    </div>
                                @elseif ($label === 'Last 7 days')
                                    <div class="small text-muted">Previous 7 days: {{ $fmt($k['prev7']) }}</div>
                                @endif
                            </div>
                        </a>
                    </div>
                @endforeach
                @php $open = (bool) ($m['setting']->registration_enabled ?? true); @endphp
                <div class="col-sm-6 d-flex">
                    <a href="{{ route('registration.edit') }}" class="card dash-tile w-100 text-reset">
                        <div class="card-body">
                            <div class="dash-label">Registration</div>
                            <div class="dash-value">
                                @if ($open)
                                    <i class="fas fa-check-circle text-success" aria-hidden="true"></i> Open
                                @else
                                    <i class="fas fa-pause-circle text-secondary" aria-hidden="true"></i> Closed
                                @endif
                            </div>
                            <div class="small text-muted">Change in Registration</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ Row 2: daily chart + site health ============ --}}
    <div class="row">
        <div class="col-lg-8 d-flex">
            <div class="card w-100">
                <div class="card-header">
                    <h3 class="card-title">Registrations per day <small class="text-muted">– last 30 days</small></h3>
                </div>
                <div class="card-body">
                    @php
                        $series = $m['daily'];
                        $maxCount = max(array_column($series, 'count')) ?: 0;
                        // clean axis maximum: 1, 2, 5 x 10^n
                        $niceMax = 4;
                        if ($maxCount > 0) {
                            $pow = pow(10, floor(log10($maxCount)));
                            foreach ([1, 2, 2.5, 5, 10] as $step) {
                                if ($step * $pow >= $maxCount) { $niceMax = $step * $pow; break; }
                            }
                            $niceMax = max(4, $niceMax);
                        }
                        $W = 800; $H = 220; $left = 40; $right = 8; $top = 10; $bottom = 26;
                        $plotW = $W - $left - $right; $plotH = $H - $top - $bottom;
                        $slot = $plotW / count($series);
                        $barW = min(20, $slot - 2);
                        $ticks = [0, $niceMax / 4, $niceMax / 2, $niceMax * 3 / 4, $niceMax];
                    @endphp
                    @if ($maxCount === 0)
                        <p class="text-muted mb-0">No registrations in the last 30 days.</p>
                    @else
                        <div class="dash-chart" id="dailyChart">
                            <svg viewBox="0 0 {{ $W }} {{ $H }}" role="img" aria-label="Registrations per day for the last 30 days, highest {{ $maxCount }}">
                                @foreach ($ticks as $t)
                                    @php $y = $top + $plotH - ($t / $niceMax) * $plotH; @endphp
                                    <line x1="{{ $left }}" x2="{{ $W - $right }}" y1="{{ $y }}" y2="{{ $y }}" class="grid"/>
                                    <text x="{{ $left - 6 }}" y="{{ $y + 4 }}" text-anchor="end" class="tick">{{ fmod($t, 1) ? number_format($t, 1) : number_format($t) }}</text>
                                @endforeach
                                @foreach ($series as $i => $day)
                                    @php
                                        $cx = $left + $slot * $i + $slot / 2;
                                        $h = $day['count'] / $niceMax * $plotH;
                                        $x = $cx - $barW / 2;
                                        $yTop = $top + $plotH - $h;
                                        $r = min(4, $h, $barW / 2);
                                        $base = $top + $plotH;
                                    @endphp
                                    @if ($h > 0)
                                        <path class="bar" data-i="{{ $i }}" d="M{{ $x }},{{ $base }} V{{ $yTop + $r }} Q{{ $x }},{{ $yTop }} {{ $x + $r }},{{ $yTop }} H{{ $x + $barW - $r }} Q{{ $x + $barW }},{{ $yTop }} {{ $x + $barW }},{{ $yTop + $r }} V{{ $base }} Z"/>
                                    @endif
                                    <rect class="hit" data-i="{{ $i }}" x="{{ $left + $slot * $i }}" y="{{ $top }}" width="{{ $slot }}" height="{{ $plotH }}"
                                        data-label="{{ $day['date']->format('D, d M') }}" data-value="{{ $day['count'] }}"/>
                                    @if ($i % 5 === 0 || $i === count($series) - 1)
                                        <text x="{{ $cx }}" y="{{ $H - 8 }}" text-anchor="middle" class="tick">{{ $day['date']->format('d M') }}</text>
                                    @endif
                                @endforeach
                                <line x1="{{ $left }}" x2="{{ $W - $right }}" y1="{{ $top + $plotH }}" y2="{{ $top + $plotH }}" class="axis"/>
                            </svg>
                            <div class="dash-tooltip" hidden></div>
                        </div>
                        <details class="mt-2">
                            <summary class="small text-muted">Show as table</summary>
                            <div class="table-responsive" style="max-height: 240px">
                                <table class="table table-sm mb-0">
                                    <thead><tr><th>Day</th><th class="text-right">Registrations</th></tr></thead>
                                    <tbody>
                                        @foreach (array_reverse($series) as $day)
                                            <tr><td>{{ $day['date']->format('D, d M Y') }}</td><td class="text-right">{{ $day['count'] }}</td></tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </details>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4 d-flex">
            <div class="card w-100">
                <div class="card-header"><h3 class="card-title">Site status</h3></div>
                <ul class="list-group list-group-flush">
                    @php
                        $statusIcon = [
                            'good' => ['fa-check-circle', 'text-success', 'OK'],
                            'warning' => ['fa-exclamation-triangle', 'text-warning', 'Check'],
                            'critical' => ['fa-times-circle', 'text-danger', 'Problem'],
                            'info' => ['fa-info-circle', 'text-secondary', 'Info'],
                        ];
                    @endphp
                    @foreach ($m['checks'] as [$status, $label, $detail, $link])
                        @php [$icon, $color, $srText] = $statusIcon[$status]; @endphp
                        <li class="list-group-item d-flex py-2">
                            <i class="fas {{ $icon }} {{ $color }} mt-1 mr-2" aria-hidden="true"></i>
                            <div class="flex-fill">
                                <span class="sr-only">{{ $srText }}:</span>
                                @if ($link)
                                    <a href="{{ $link }}" class="text-body">{{ $label }}</a>
                                @else
                                    {{ $label }}
                                @endif
                                @if ($detail)
                                    <div class="small text-muted">{{ $detail }}</div>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    {{-- ============ Row 3: who is registering + by event ============ --}}
    <div class="row">
        @foreach ([['Top organisations', $m['topCompanies']], ['Top designations', $m['topDesignations']]] as [$title, $rows])
            <div class="col-lg-4 col-md-6 d-flex">
                <div class="card w-100">
                    <div class="card-header"><h3 class="card-title">{{ $title }}</h3></div>
                    <div class="card-body">
                        @if (count($rows))
                            @php $topMax = max(array_column($rows, 'total')); @endphp
                            @foreach ($rows as $row)
                                <div class="dash-hbar" title="{{ $row['label'] }}: {{ $row['total'] }}">
                                    <div class="d-flex justify-content-between small">
                                        <span class="text-truncate pr-2">{{ $row['label'] }}</span>
                                        <span class="text-muted">{{ $fmt($row['total']) }}</span>
                                    </div>
                                    <div class="dash-hbar-track"><div class="dash-hbar-fill" style="width: {{ max(2, round($row['total'] / $topMax * 100)) }}%"></div></div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted mb-0">No data yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach

        <div class="col-lg-4 d-flex">
            <div class="card w-100">
                <div class="card-header"><h3 class="card-title">Registrations by event</h3></div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Event</th><th class="text-right">Registered</th><th class="text-right">Target</th></tr></thead>
                        <tbody>
                            @forelse ($m['byEvent'] as $e)
                                <tr>
                                    <td>
                                        <a href="{{ route('schedules.edit', $e) }}" class="text-body">{{ \Illuminate\Support\Str::limit($e->title, 38) }}</a>
                                        <div class="small text-muted">{{ optional($e->date)->format('d M Y') }}</div>
                                    </td>
                                    <td class="text-right">{{ $fmt($e->bookings_count) }}</td>
                                    <td class="text-right text-muted">
                                        @if ($e->seat_target)
                                            {{ $fmt($e->seat_target) }}
                                            <div class="small">{{ min(999, round($e->bookings_count / $e->seat_target * 100)) }}%</div>
                                        @else
                                            –
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-muted">No events with registrations yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ Row 4: latest registrations + content ============ --}}
    <div class="row">
        <div class="col-lg-7 d-flex">
            <div class="card w-100">
                <div class="card-header d-flex align-items-center">
                    <h3 class="card-title">Latest registrations</h3>
                    <a href="{{ route('booking.index') }}" class="ml-auto small">All registrations &amp; CSV &rarr;</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead><tr><th>Name</th><th>Organisation</th><th>Event</th><th class="text-right">When</th></tr></thead>
                            <tbody>
                                @forelse ($m['latest'] as $b)
                                    <tr>
                                        <td>{{ $b->name }}<div class="small text-muted">{{ $b->designation }}</div></td>
                                        <td>{{ $b->company }}</td>
                                        <td class="small">{{ $b->event ?: '–' }}</td>
                                        <td class="text-right small text-nowrap" title="{{ $b->created_at?->format('d M Y, g:i A') }}">{{ $b->created_at?->diffForHumans() }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-muted">No registrations yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5 d-flex">
            <div class="card w-100">
                <div class="card-header"><h3 class="card-title">Website content</h3></div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($m['content'] as [$label, $count, $icon, $link])
                            <div class="col-6 mb-2">
                                <a href="{{ $link }}" class="d-flex align-items-center text-body dash-content">
                                    <span class="dash-content-icon"><i class="{{ $icon }}" aria-hidden="true"></i></span>
                                    <span><strong>{{ $fmt($count) }}</strong> <span class="small text-muted d-block">{{ $label }}</span></span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ Row 5: popular pages + logins ============ --}}
    <div class="row">
        @foreach ([['Most viewed events', $m['popularEvents'], 'service.detail'], ['Most viewed blog posts', $m['popularPosts'], 'blog.detail']] as [$title, $rows, $routeName])
            <div class="col-lg-4 col-md-6 d-flex">
                <div class="card w-100">
                    <div class="card-header"><h3 class="card-title">{{ $title }}</h3></div>
                    <ul class="list-group list-group-flush">
                        @forelse ($rows as $row)
                            <li class="list-group-item d-flex justify-content-between py-2">
                                <a href="{{ route($routeName, $row->slug) }}" target="_blank" class="text-body text-truncate pr-2">{{ $row->title }}</a>
                                <span class="text-muted text-nowrap">{{ $fmt($row->views) }} views</span>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">Nothing published yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        @endforeach

        <div class="col-lg-4 d-flex">
            <div class="card w-100">
                <div class="card-header d-flex align-items-center">
                    <h3 class="card-title">Admin sign-ins</h3>
                    @if ($m['logins']['failed7'])
                        <span class="ml-auto badge badge-warning">
                            <i class="fas fa-exclamation-triangle"></i> {{ $m['logins']['failed7'] }} failed in 7 days
                        </span>
                    @endif
                </div>
                <ul class="list-group list-group-flush">
                    @forelse ($m['logins']['recent'] as $login)
                        <li class="list-group-item d-flex justify-content-between py-2 small">
                            <span>
                                @if ($login->login_successful)
                                    <i class="fas fa-check-circle text-success" aria-hidden="true"></i> Signed in
                                @else
                                    <i class="fas fa-times-circle text-danger" aria-hidden="true"></i> Failed attempt
                                @endif
                                <span class="text-muted">· {{ $login->ip_address }}</span>
                            </span>
                            <span class="text-muted text-nowrap">{{ \Carbon\Carbon::parse($login->login_at)->diffForHumans() }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">No sign-ins recorded.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .dash-label { font-size: .8rem; color: #6c757d; margin-bottom: .15rem; }
        .dash-hero { font-size: 3.25rem; font-weight: 600; line-height: 1.05; font-variant-numeric: proportional-nums; }
        .dash-value { font-size: 1.75rem; font-weight: 600; line-height: 1.2; }
        .dash-tile { transition: box-shadow .15s; }
        .dash-tile:hover { box-shadow: 0 0 0 2px #2a78d6; text-decoration: none; }
        .dash-meter { height: 8px; border-radius: 4px; background: #d6e6f8; overflow: hidden; }
        .dash-meter-fill { height: 100%; background: #2a78d6; border-radius: 4px; }
        .dash-meter-fill.is-full { background: #1f8a4c; }
        .dash-chart { position: relative; }
        .dash-chart svg { width: 100%; height: auto; display: block; }
        .dash-chart .grid { stroke: #eceff1; stroke-width: 1; }
        .dash-chart .axis { stroke: #ced4da; stroke-width: 1; }
        .dash-chart .tick { fill: #6c757d; font-size: 11px; font-variant-numeric: tabular-nums; }
        .dash-chart .bar { fill: #2a78d6; }
        .dash-chart .bar.is-hover { fill: #1d5fae; }
        .dash-chart .hit { fill: transparent; cursor: default; }
        .dash-tooltip { position: absolute; pointer-events: none; background: #212529; color: #fff; font-size: 12px; padding: 4px 8px; border-radius: 4px; white-space: nowrap; transform: translate(-50%, -110%); }
        .dash-hbar { margin-bottom: .6rem; }
        .dash-hbar-track { height: 8px; }
        .dash-hbar-fill { height: 8px; background: #2a78d6; border-radius: 0 4px 4px 0; }
        .dash-content { padding: .35rem; border-radius: .25rem; }
        .dash-content:hover { background: #f4f6f9; text-decoration: none; }
        .dash-content-icon { width: 2.25rem; height: 2.25rem; border-radius: 50%; background: #e9f1fb; color: #2a78d6; display: inline-flex; align-items: center; justify-content: center; margin-right: .6rem; flex: none; }
    </style>
@stop

@section('js')
    <script>
        (function() {
            var chart = document.getElementById('dailyChart');
            if (!chart) { return; }
            var tip = chart.querySelector('.dash-tooltip');
            var svg = chart.querySelector('svg');
            var hits = chart.querySelectorAll('.hit');
            hits.forEach(function(hit) {
                hit.addEventListener('mousemove', function() {
                    var n = parseInt(hit.dataset.value, 10);
                    tip.textContent = hit.dataset.label + ': ' + n + (n === 1 ? ' registration' : ' registrations');
                    var box = hit.getBoundingClientRect();
                    var outer = chart.getBoundingClientRect();
                    tip.style.left = (box.left - outer.left + box.width / 2) + 'px';
                    tip.style.top = (box.top - outer.top + 14) + 'px';
                    tip.hidden = false;
                    var bar = chart.querySelector('.bar[data-i="' + hit.dataset.i + '"]');
                    if (bar) { bar.classList.add('is-hover'); }
                });
                hit.addEventListener('mouseleave', function() {
                    tip.hidden = true;
                    var bar = chart.querySelector('.bar[data-i="' + hit.dataset.i + '"]');
                    if (bar) { bar.classList.remove('is-hover'); }
                });
            });
        })();
    </script>
@stop
