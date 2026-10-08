@extends('adminlte::page')

@section('title', 'Years')

@section('content_header')
    <h1>Years</h1>
@stop

@section('content')
    @include('backend.partials.alerts')

    <p class="text-muted">
        Each year groups its speakers, sponsors &amp; exhibitors, gallery images and videos, and points to that year's event
        for the Schedule. A year appears in a header flyout (Speakers › 2026, Gallery › 2025 › Images…) only when it has
        content for that tab. Switch a year to <strong>Hidden</strong> to take it off the website without deleting anything.
    </p>

    <div class="card card-success card-outline">
        <div class="card-header"><h3 class="card-title">Add a year</h3></div>
        <div class="card-body">
            <form action="{{ route('editions.store') }}" method="post" class="form-row align-items-end">
                @csrf
                <div class="col-md-2 my-1">
                    <label class="small mb-0" for="new_year">Year <span class="text-danger">*</span></label>
                    <input type="number" name="year" id="new_year" class="form-control" min="2000" max="2100" required
                        value="{{ old('year', $suggestedYear) }}">
                </div>
                <div class="col-md-4 my-1">
                    <label class="small mb-0" for="new_title">Name <span class="text-muted">(optional)</span></label>
                    <input type="text" name="title" id="new_title" class="form-control" maxlength="191"
                        value="{{ old('title') }}" placeholder="e.g. India Law, AI & Tech Summit {{ $suggestedYear }}">
                </div>
                <div class="col-md-4 my-1">
                    <label class="small mb-0" for="new_event">Schedule = this event's page</label>
                    <select name="schedule_service_id" id="new_event" class="form-control">
                        <option value="">– none yet –</option>
                        @foreach ($events as $event)
                            <option value="{{ $event->id }}" @selected((string) old('schedule_service_id') === (string) $event->id)>
                                {{ $event->title }}{{ $event->date ? ' (' . $event->date->format('M Y') . ')' : '' }}{{ $event->published === '1' ? '' : ' – unpublished' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <input type="hidden" name="is_visible" value="1">
                <div class="col-md-2 my-1">
                    <button type="submit" class="btn btn-success btn-block">+ Add year</button>
                </div>
            </form>
        </div>
    </div>

    <form method="get" class="mb-3" style="max-width: 360px">
        <div class="input-group input-group-sm">
            <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search years or names…">
            <div class="input-group-append"><button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button></div>
        </div>
    </form>

    <div class="card">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover mb-0 editions-table">
                <thead>
                    <tr>
                        <th>Year</th>
                        <th>Schedule (event page)</th>
                        <th class="text-center">Speakers</th>
                        <th class="text-center">Sponsors</th>
                        <th class="text-center">Images</th>
                        <th class="text-center">Videos</th>
                        <th style="width: 120px">On site</th>
                        <th style="width: 150px"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($editions as $edition)
                        <tr data-visibility-row class="{{ $edition->is_visible ? '' : 'is-hidden' }}">
                            <td class="vs-dim">
                                <strong style="font-size: 1.1rem">{{ $edition->year }}</strong>
                                @if ($edition->title)<div class="small text-muted">{{ $edition->title }}</div>@endif
                            </td>
                            <td class="vs-dim">
                                @if ($edition->scheduleService)
                                    <a href="{{ route('service.detail', $edition->scheduleService->slug) }}" target="_blank" rel="noopener">{{ $edition->scheduleService->title }}</a>
                                    @if ($edition->scheduleService->published !== '1')
                                        <span class="badge badge-warning">unpublished</span>
                                    @endif
                                @else
                                    <span class="text-muted small">none – no Schedule link for this year</span>
                                @endif
                            </td>
                            <td class="text-center vs-dim">
                                <a href="{{ route('team.index', ['year' => $edition->id, 'role' => 'Speaker']) }}">{{ $edition->speakers_count }}</a>
                                @if ($edition->teams_count > $edition->speakers_count)
                                    <div class="small text-muted" title="Advisors and organisers tagged with this year">+{{ $edition->teams_count - $edition->speakers_count }} other</div>
                                @endif
                            </td>
                            <td class="text-center vs-dim"><a href="{{ route('sponsors.index', ['year' => $edition->id]) }}">{{ $edition->sponsors_count }}</a></td>
                            <td class="text-center vs-dim"><a href="{{ route('gallery.index', ['year' => $edition->id]) }}">{{ $edition->galleries_count }}</a></td>
                            <td class="text-center vs-dim"><a href="{{ route('video.index', ['year' => $edition->id]) }}">{{ $edition->videos_count }}</a></td>
                            <td>
                                @include('backend.partials.visibility-switch', [
                                    'action' => route('editions.toggle', $edition),
                                    'id' => 'vis-edition-' . $edition->id,
                                    'visible' => $edition->is_visible,
                                    'name' => (string) $edition->year,
                                ])
                            </td>
                            <td class="text-nowrap text-right">
                                <a href="{{ route('editions.edit', $edition) }}" class="btn btn-sm btn-info"><i class="fas fa-pencil-alt"></i> Edit</a>
                                <form action="{{ route('editions.destroy', $edition) }}" method="post" class="d-inline"
                                    onsubmit="return confirm('Delete the year {{ $edition->year }}?');">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">No years{{ request('q') ? ' match your search' : ' yet' }}.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('js')
    @include('backend.partials.visibility-switch-js')
@stop

@section('css')
    <style>.editions-table td { vertical-align: middle; }</style>
@stop
