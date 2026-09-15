@extends('adminlte::page')

@section('title', 'Schedules')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1>Event schedules</h1>
        <a href="{{ route('page-content.edit', 'schedule') }}" class="btn btn-outline-secondary btn-sm">Headings &amp; buttons for all schedule pages</a>
    </div>
@stop

@section('content')
    @include('backend.partials.alerts')

    <p class="text-muted">
        Edit each event's date, timings, venue, sessions, speakers and agenda PDF. To add a new event, use
        <a href="{{ route('service.create') }}">Events › Add New</a>, then come back here to build its schedule.
    </p>

    <div class="card card-primary card-outline">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Date &amp; time</th>
                            <th class="text-right">Sessions</th>
                            <th class="text-right">Registrations</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($events as $event)
                            @php
                                $sessionCount = count($event->sessions());
                                $upcoming = $event->date && $event->date->isFuture();
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $event->title }}</strong><br>
                                    <a href="{{ route('service.detail', $event->slug) }}" target="_blank" class="small">/event/{{ $event->slug }}</a>
                                </td>
                                <td class="text-nowrap">
                                    @if ($event->date)
                                        {{ $event->date->format('D, d M Y') }}<br>
                                        <small class="text-muted">
                                            {{ $event->date->format('g:i A') }}@if ($event->end_time) – {{ \App\Support\Schedule::formatTime($event->end_time) }}@endif
                                        </small>
                                    @else
                                        <span class="text-muted">Not set</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    {{ $sessionCount }}
                                    @if (!$sessionCount && $upcoming)
                                        <br><small class="text-warning"><i class="fas fa-exclamation-triangle"></i> none yet</small>
                                    @endif
                                </td>
                                <td class="text-right">
                                    {{ number_format($event->bookings_count) }}@if ($event->seat_target) <small class="text-muted">/ {{ number_format($event->seat_target) }}</small>@endif
                                </td>
                                <td>
                                    @if ($event->published != '1')
                                        <span class="badge badge-secondary">Hidden</span>
                                    @elseif ($upcoming)
                                        <span class="badge badge-success">Upcoming</span>
                                    @else
                                        <span class="badge badge-light">Past</span>
                                    @endif
                                </td>
                                <td class="text-right text-nowrap">
                                    <a href="{{ route('schedules.edit', $event) }}" class="btn btn-sm btn-primary">Edit schedule</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">No events yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
