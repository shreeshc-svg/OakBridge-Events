@extends('adminlte::page')

@section('title', 'Cancelled passes')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1>Cancelled passes <small class="text-muted">{{ number_format($total) }} in total</small></h1>
        <a href="{{ route('booking.index') }}" class="btn btn-outline-secondary btn-sm">&larr; Booking</a>
    </div>
@stop

@section('content')
    <p class="text-muted">
        Every pass that was cancelled or deleted – from the Booking list, Complimentary passes or by deleting an order – is kept here,
        so every pass number can be accounted for. Pass numbers are never re-used.
    </p>

    <form method="get" class="card card-body py-2 mb-3">
        <div class="form-row align-items-end">
            <div class="col-md-4 my-1">
                <label class="small mb-0" for="q">Search</label>
                <input type="search" name="q" id="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Pass no., name, email, order no.…">
            </div>
            <div class="col-md-2 my-1">
                <label class="small mb-0" for="kind">Type</label>
                <select name="kind" id="kind" class="form-control form-control-sm" onchange="this.form.submit()">
                    <option value="">All</option>
                    @foreach ($kinds as $k)<option value="{{ $k }}" @selected(request('kind') === $k)>{{ $k }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-3 my-1">
                <label class="small mb-0" for="event">Event</label>
                <select name="event" id="event" class="form-control form-control-sm" onchange="this.form.submit()">
                    <option value="">All</option>
                    @foreach ($events as $e)<option value="{{ $e }}" @selected(request('event') === $e)>{{ $e }}</option>@endforeach
                </select>
            </div>
            <div class="col-auto my-1">
                <button class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
                <a href="{{ route('cancelled-passes.export', request()->only('q', 'kind', 'event')) }}" class="btn btn-outline-success btn-sm"><i class="fas fa-download"></i> CSV</a>
                @if (request()->hasAny(['q', 'kind', 'event']))<a href="{{ route('cancelled-passes.index') }}" class="btn btn-link btn-sm">Clear</a>@endif
            </div>
        </div>
    </form>

    <div class="card">
        <div class="card-body p-0 table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="thead-light">
                    <tr><th>Pass no.</th><th>Holder</th><th>Type</th><th>Event</th><th>Issued</th><th>Cancelled</th><th>By</th><th>Where</th></tr>
                </thead>
                <tbody>
                    @forelse ($passes as $p)
                        <tr>
                            <td class="font-weight-bold text-nowrap">{{ $p->booking_id }}</td>
                            <td>
                                {{ $p->name }}
                                <div class="small text-muted">{{ $p->email }}@if ($p->phone) · {{ $p->phone }}@endif</div>
                            </td>
                            <td class="small">
                                {{ $p->kind }}
                                @if ($p->pass_name)<div class="text-muted">{{ $p->pass_name }}</div>@endif
                                @if ($p->order_no)<div class="text-muted">Order {{ $p->order_no }}</div>@endif
                            </td>
                            <td class="small">{{ $p->event }}</td>
                            <td class="small text-nowrap">{{ $p->issued_at?->format('j M Y, H:i') }}</td>
                            <td class="small text-nowrap">{{ $p->cancelled_at?->format('j M Y, H:i') }}</td>
                            <td class="small">{{ $p->cancelled_by_name }}</td>
                            <td class="small">{{ $p->source }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">{{ $total ? 'Nothing matches.' : 'No pass has been cancelled since this log started.' }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($passes->hasPages())
            <div class="card-footer">{{ $passes->links() }}</div>
        @endif
    </div>
@stop
