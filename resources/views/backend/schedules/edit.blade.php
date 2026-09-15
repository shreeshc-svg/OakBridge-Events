@extends('adminlte::page')

@section('title', 'Schedule – ' . $service->title)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1>Schedule <small class="text-muted">› {{ $service->title }}</small></h1>
        <div>
            <a href="{{ route('service.detail', $service->slug) }}" target="_blank" class="btn btn-outline-secondary btn-sm">View page</a>
            <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary btn-sm">&larr; All schedules</a>
        </div>
    </div>
@stop

@section('content')
    @include('backend.partials.alerts')

    <form action="{{ route('schedules.update', $service) }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card card-primary card-outline">
                    <div class="card-header"><h3 class="card-title">Event</h3></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="title">Event title <small class="text-muted">(page banner)</small></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title"
                                value="{{ old('title', $service->title) }}" maxlength="200" required>
                            <small class="form-text text-muted">Page address stays <code>/event/{{ $service->slug }}</code>.</small>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="event_date">Date</label>
                                <input type="date" class="form-control @error('event_date') is-invalid @enderror" id="event_date"
                                    name="event_date" value="{{ old('event_date', optional($service->date)->format('Y-m-d')) }}" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="start_time">Starts</label>
                                <input type="time" class="form-control @error('start_time') is-invalid @enderror" id="start_time"
                                    name="start_time" value="{{ old('start_time', optional($service->date)->format('H:i')) }}" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="end_time">Ends <small class="text-muted">(optional)</small></label>
                                <input type="time" class="form-control @error('end_time') is-invalid @enderror" id="end_time"
                                    name="end_time" value="{{ old('end_time', $service->end_time) }}">
                            </div>
                        </div>
                        <small class="form-text text-muted mt-n2 mb-3">Only events dated in the future appear in the registration form.</small>
                        <div class="form-row">
                            <div class="form-group col-md-8">
                                <label for="venue">Venue</label>
                                <input type="text" class="form-control" id="venue" name="venue" maxlength="255"
                                    value="{{ old('venue', $service->venue) }}" placeholder="{{ $setting->address ?? '' }}">
                                <small class="form-text text-muted">Leave empty to show the address from Settings.</small>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="seat_target">Registration target</label>
                                <input type="number" class="form-control" id="seat_target" name="seat_target" min="1"
                                    value="{{ old('seat_target', $service->seat_target) }}" placeholder="e.g. 500">
                                <small class="form-text text-muted">Shown as progress on the dashboard.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-primary card-outline">
                    <div class="card-header d-flex align-items-center">
                        <h3 class="card-title mb-0">Sessions</h3>
                        <small class="text-muted ml-2">shown in this order</small>
                    </div>
                    <div class="card-body">
                        <div id="sessionList">
                            @foreach ($sessions as $i => $session)
                                @include('backend.schedules.session-row', ['i' => $i, 'session' => $session])
                            @endforeach
                        </div>
                        <p class="text-muted" id="noSessions" @if (count($sessions)) hidden @endif>
                            No sessions yet. Until you add some, the page says the schedule will be announced soon.
                        </p>
                        <button type="button" class="btn btn-outline-primary" id="addSession">+ Add session</button>
                        <template id="sessionTemplate">
                            @include('backend.schedules.session-row', ['i' => '__INDEX__', 'session' => []])
                        </template>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card card-primary card-outline sticky-top" style="top: 1rem; z-index: 1">
                    <div class="card-body">
                        <div class="custom-control custom-switch mb-3">
                            <input type="checkbox" class="custom-control-input" id="published" name="published" value="1"
                                @checked(old('published', $service->published) == '1')>
                            <label class="custom-control-label" for="published">Published</label>
                        </div>

                        <div class="form-group">
                            <label>Agenda PDF</label>
                            @if ($service->agenda_file)
                                <div class="small mb-1">
                                    <a href="{{ \App\Support\Uploads::url($service->agenda_file) }}" target="_blank">{{ basename($service->agenda_file) }}</a>
                                </div>
                                <div class="custom-control custom-checkbox mb-1">
                                    <input type="checkbox" class="custom-control-input" id="remove_agenda" name="remove_agenda" value="1">
                                    <label class="custom-control-label small" for="remove_agenda">Remove (use the default agenda)</label>
                                </div>
                            @else
                                <div class="small text-muted mb-1">Using the default agenda from Page Content › Schedule.</div>
                            @endif
                            <input type="file" class="form-control-file" name="agenda_file" accept=".pdf">
                        </div>

                        <hr>
                        <div class="form-group">
                            <label for="meta_title">SEO title</label>
                            <input type="text" class="form-control form-control-sm" id="meta_title" name="meta_title" maxlength="255"
                                value="{{ old('meta_title', $service->meta_title) }}" placeholder="{{ $service->title }} - {{ $setting->site_title ?? '' }}">
                        </div>
                        <div class="form-group">
                            <label for="meta_description">SEO description</label>
                            <textarea class="form-control form-control-sm" id="meta_description" name="meta_description" rows="3"
                                maxlength="1000">{{ old('meta_description', $service->meta_description) }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Save schedule</button>
                        <small class="d-block text-muted mt-2">
                            The event's image, summary and categories are edited in
                            <a href="{{ route('service.edit', $service) }}">Events › Edit</a>.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <datalist id="sessionTypes">
        <option value="Keynote Address">
        <option value="Panel Discussion">
        <option value="Fireside Chat">
        <option value="Workshop">
        <option value="Networking Break">
        <option value="Lunch">
        <option value="Inaugural Session">
        <option value="Valedictory Session">
    </datalist>
@stop

@section('css')
    <style>
        .session-row { border: 1px solid #dee2e6; border-left: 4px solid #007bff; border-radius: .25rem; padding: .75rem .75rem .25rem; margin-bottom: .75rem; background: #fcfcfc; }
    </style>
@stop

@section('js')
    <script>
        (function() {
            var list = document.getElementById('sessionList');
            var tpl = document.getElementById('sessionTemplate').innerHTML;
            var empty = document.getElementById('noSessions');
            var addBtn = document.getElementById('addSession');
            var counter = 1000;

            function renumber() {
                Array.prototype.forEach.call(list.children, function(row, index) {
                    row.querySelectorAll('[data-field]').forEach(function(input) {
                        input.name = 'sessions[' + index + '][' + input.dataset.field + ']';
                    });
                    row.querySelector('.session-number').textContent = 'Session ' + (index + 1);
                });
                empty.hidden = list.children.length > 0;
                addBtn.disabled = list.children.length >= {{ \App\Support\Schedule::MAX_SESSIONS }};
            }

            function newRow(values) {
                var wrapper = document.createElement('div');
                wrapper.innerHTML = tpl.replace(/__INDEX__/g, counter++).trim();
                var row = wrapper.firstElementChild;
                if (values) {
                    row.querySelectorAll('[data-field]').forEach(function(input) {
                        input.value = values[input.dataset.field] || '';
                    });
                }
                return row;
            }

            addBtn.addEventListener('click', function() {
                var row = newRow();
                list.appendChild(row);
                renumber();
                row.querySelector('[data-field="from"]').focus();
            });

            list.addEventListener('click', function(e) {
                var btn = e.target.closest('button[data-action]');
                if (!btn) { return; }
                var row = btn.closest('.session-row');
                var action = btn.dataset.action;
                if (action === 'remove') {
                    if (!confirm('Remove this session?')) { return; }
                    row.remove();
                }
                if (action === 'up' && row.previousElementSibling) { list.insertBefore(row, row.previousElementSibling); }
                if (action === 'down' && row.nextElementSibling) { list.insertBefore(row.nextElementSibling, row); }
                if (action === 'copy') {
                    var values = {};
                    row.querySelectorAll('[data-field]').forEach(function(input) { values[input.dataset.field] = input.value; });
                    list.insertBefore(newRow(values), row.nextElementSibling);
                }
                renumber();
            });

            renumber();
        })();
    </script>
@stop
