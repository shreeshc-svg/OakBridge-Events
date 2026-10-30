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

    @if ($canReorder)
        <div class="alert alert-light border d-flex align-items-center flex-wrap mb-3" id="reorderHelp">
            <i class="fas fa-arrows-alt-v text-primary mr-2"></i>
            <span class="mr-2">Type an order number (<strong>1</strong> = first) and press Enter, drag <i class="fas fa-grip-vertical text-muted"></i>, or use the arrows. It saves by itself.</span>
            <span class="small ml-auto" id="reorderStatus" role="status" aria-live="polite"></span>
        </div>
    @else
        @php
            $steps = [];
            if (! $role) { $steps[] = 'pick a <strong>Type</strong> (and a <strong>Year</strong> for speakers)'; }
            elseif ($role === 'Speaker' && ! request('year')) { $steps[] = 'pick a <strong>Year</strong>'; }
            if (request('q')) { $steps[] = 'clear the search'; }
        @endphp
        <p class="small text-muted mb-2" id="reorderHint">
            <i class="fas fa-sort"></i> To change the order on the website, {!! implode(' and ', $steps) !!}.
        </p>
    @endif

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
                        @if ($canReorder)<th style="width: 128px">Order</th>@endif
                        <th style="width: 36px"><input type="checkbox" class="bulk-all" data-form="bulkTeam" aria-label="Select all on this page"></th>
                        <th style="width: 80px">Photo</th>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Type</th>
                        <th>Year</th>
                        <th style="width: 170px"></th>
                    </tr>
                </thead>
                <tbody @if ($canReorder) id="teamOrder" @endif>
                    @forelse ($teams as $team)
                        <tr @if ($canReorder) data-id="{{ $team->id }}" @endif>
                            @if ($canReorder)
                                <td class="text-nowrap">
                                    <span class="drag-handle" title="Drag to reorder" aria-hidden="true"><i class="fas fa-grip-vertical"></i></span>
                                    <input type="number" class="form-control form-control-sm order-no" min="1" max="{{ $teams->count() }}" step="1"
                                        inputmode="numeric" value="{{ $loop->iteration }}" aria-label="Order number of {{ $team->name }}" title="Type a number and press Enter">
                                    <span class="btn-group-vertical btn-group-sm align-middle ml-1">
                                        <button type="button" class="btn btn-light btn-xs order-up" title="Move up" aria-label="Move {{ $team->name }} up"><i class="fas fa-chevron-up"></i></button>
                                        <button type="button" class="btn btn-light btn-xs order-down" title="Move down" aria-label="Move {{ $team->name }} down"><i class="fas fa-chevron-down"></i></button>
                                    </span>
                                </td>
                            @endif
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
                        <tr><td colspan="{{ $canReorder ? 8 : 7 }}" class="text-center text-muted py-4">Nobody matches these filters.</td></tr>
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
    <style>
        .year-table td { vertical-align: middle; }
        #teamOrder .drag-handle { cursor: grab; color: #adb5bd; padding: 4px 6px; }
        #teamOrder .drag-handle:hover { color: #007bff; }
        #teamOrder tr.dragging { opacity: .45; background: #e9f2ff; }
        #teamOrder .btn-xs { padding: 0 6px; font-size: 10px; line-height: 1.5; }
        #teamOrder .order-no { display: inline-block; width: 58px; padding: 2px 4px; text-align: center; vertical-align: middle; -moz-appearance: textfield; }
        #teamOrder .order-no::-webkit-inner-spin-button, #teamOrder .order-no::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
        #teamOrder tr.just-moved { background: #fff8db; transition: background .6s; }
    </style>
@stop

@section('js')
    @include('backend.partials.bulk-bar-js')
    @if ($canReorder)
        <script>
            (function () {
                var body = document.getElementById('teamOrder');
                var status = document.getElementById('reorderStatus');
                var dragged = null, saved = order(), timer = null;

                function rows() { return Array.prototype.slice.call(body.querySelectorAll('tr[data-id]')); }
                function order() { return rows().map(function (r) { return parseInt(r.dataset.id, 10); }); }
                function number() { rows().forEach(function (r, i) { r.querySelector('.order-no').value = i + 1; }); }
                function say(text, cls) { status.textContent = text; status.className = 'small ml-auto ' + cls; }

                function save() {
                    number();
                    var now = order();
                    if (now.join() === saved.join()) { return; }
                    say('Saving…', 'text-muted');
                    fetch(@json(route('team.reorder')), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ ids: now })
                    }).then(function (r) {
                        if (!r.ok) { throw new Error(r.status); }
                        return r.json();
                    }).then(function (data) {
                        saved = now;
                        say(data.message || 'Order saved.', 'text-success');
                    }).catch(function () {
                        say('Could not save the new order. Reload the page and try again.', 'text-danger font-weight-bold');
                    });
                }
                function flash(row) {
                    row.classList.add('just-moved');
                    setTimeout(function () { row.classList.remove('just-moved'); }, 700);
                }

                // drag by the handle only, so text and buttons in the row still work
                body.addEventListener('mousedown', function (e) {
                    var row = e.target.closest('tr[data-id]');
                    if (row) { row.draggable = !!e.target.closest('.drag-handle'); }
                });
                body.addEventListener('dragstart', function (e) {
                    dragged = e.target.closest('tr[data-id]');
                    if (!dragged) { return; }
                    dragged.classList.add('dragging');
                    e.dataTransfer.effectAllowed = 'move';
                    e.dataTransfer.setData('text/plain', dragged.dataset.id);
                });
                body.addEventListener('dragover', function (e) {
                    if (!dragged) { return; }
                    e.preventDefault();
                    var before = null, list = rows().filter(function (r) { return r !== dragged; });
                    for (var i = 0; i < list.length; i++) {
                        var box = list[i].getBoundingClientRect();
                        if (e.clientY < box.top + box.height / 2) { before = list[i]; break; }
                    }
                    body.insertBefore(dragged, before);
                });
                body.addEventListener('drop', function (e) { e.preventDefault(); });
                body.addEventListener('dragend', function () {
                    if (!dragged) { return; }
                    dragged.classList.remove('dragging');
                    dragged.draggable = false;
                    flash(dragged);
                    dragged = null;
                    save();
                });

                // position box: type a number, Enter (or leave the box) moves the person there
                function moveTo(input) {
                    var row = input.closest('tr[data-id]'), list = rows(), from = list.indexOf(row) + 1;
                    var to = parseInt(input.value, 10);
                    if (isNaN(to)) { input.value = from; return; }
                    to = Math.min(Math.max(to, 1), list.length);
                    if (to === from) { input.value = from; return; }
                    var others = list.filter(function (r) { return r !== row; });
                    body.insertBefore(row, others[to - 1] || null);
                    flash(row);
                    save();
                    row.scrollIntoView({ block: 'center', behavior: 'smooth' });
                }
                body.addEventListener('keydown', function (e) {
                    if (e.target.classList.contains('order-no') && e.key === 'Enter') {
                        e.preventDefault(); // not the bulk form
                        moveTo(e.target);
                        e.target.select();
                    }
                });
                body.addEventListener('change', function (e) {
                    if (e.target.classList.contains('order-no')) { moveTo(e.target); }
                });
                body.addEventListener('focusin', function (e) {
                    if (e.target.classList.contains('order-no')) { e.target.select(); }
                });

                // arrows: one place up or down (also works on phones and tablets)
                body.addEventListener('click', function (e) {
                    var up = e.target.closest('.order-up'), down = e.target.closest('.order-down');
                    if (!up && !down) { return; }
                    var row = e.target.closest('tr[data-id]');
                    if (up && row.previousElementSibling) { body.insertBefore(row, row.previousElementSibling); }
                    else if (down && row.nextElementSibling) { body.insertBefore(row.nextElementSibling, row); }
                    else { return; }
                    flash(row);
                    (up || down).focus();
                    clearTimeout(timer);
                    timer = setTimeout(save, 400); // several clicks in a row: one save
                });
            })();
        </script>
    @endif
@stop
