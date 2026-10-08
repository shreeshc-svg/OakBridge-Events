@extends('adminlte::page')

@section('title', 'Speakers / Advisors / Team')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1>Speakers / Advisors / Team</h1>
        <a href="{{ route('team.create') }}" class="btn btn-primary">+ Add new</a>
    </div>
@stop

@section('content')
    @include('backend.partials.alerts')

    @include('backend.partials.year-filter', [
        'action' => route('team.index'),
        'years' => $years,
        'searchPlaceholder' => 'Name or position…',
        'extraFilters' => ['role' => ['label' => 'Type', 'options' => \App\Http\Controllers\TeamController::ROLES]],
    ])

    @include('backend.partials.bulk-bar', [
        'formId' => 'bulkTeam', 'action' => route('team.bulk'), 'years' => $years, 'noun' => 'team member', 'canCopy' => true,
    ])

    <div class="card">
        <div class="card-header py-2">
            <span class="text-muted small">
                {{ $teams->total() }} {{ \Illuminate\Support\Str::plural('person', $teams->total()) }}
                @if ($teams->total() > $teams->count()) – showing {{ $teams->firstItem() }}–{{ $teams->lastItem() }} @endif
            </span>
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover mb-0 year-table">
                <thead>
                    <tr>
                        <th style="width: 36px"><input type="checkbox" class="bulk-all" data-form="bulkTeam" aria-label="Select all on this page"></th>
                        <th style="width: 80px">Photo</th>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Type</th>
                        <th>Year</th>
                        <th style="width: 170px"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($teams as $team)
                        <tr>
                            <td><input type="checkbox" class="bulk-check" form="bulkTeam" name="ids[]" value="{{ $team->id }}" aria-label="Select {{ $team->name }}"></td>
                            <td>
                                <img src="{{ asset('public/uploads/images/' . ($team->image ? 'team/' . $team->image : 'no-image.jpg')) }}"
                                    alt="" class="rounded" style="width: 56px; height: 56px; object-fit: cover">
                            </td>
                            <td>
                                <a href="{{ route('team.edit', $team) }}" class="font-weight-bold">{{ $team->name }}</a>
                                @if ($team->year === 'Speaker')
                                    <a href="{{ route('speaker.detail', $team->id) }}" target="_blank" rel="noopener" class="small text-muted ml-1" title="View on website"><i class="fas fa-external-link-alt"></i></a>
                                @endif
                            </td>
                            <td class="small">{{ $team->position }}</td>
                            <td><span class="badge badge-light border">{{ \App\Http\Controllers\TeamController::ROLES[$team->year] ?? $team->year }}</span></td>
                            <td>
                                @if ($team->edition)
                                    <span class="badge badge-primary">{{ $team->edition->year }}</span>
                                @else
                                    <span class="badge badge-warning">no year</span>
                                @endif
                            </td>
                            <td class="text-nowrap text-right">
                                <a class="btn btn-info btn-sm" href="{{ route('team.edit', $team) }}"><i class="fas fa-pencil-alt"></i> Edit</a>
                                <form action="{{ route('team.destroy', $team) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Delete {{ addslashes($team->name) }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">Nobody matches these filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($teams->hasPages())
            <div class="card-footer">{{ $teams->links('pagination::bootstrap-4') }}</div>
        @endif
    </div>
@stop

@section('css')
    <style>.year-table td { vertical-align: middle; }</style>
@stop

@section('js')
    @include('backend.partials.bulk-bar-js')
@stop
