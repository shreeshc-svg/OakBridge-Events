@extends('adminlte::page')

@section('title', 'Sponsors & Exhibitors')

@section('content_header')
    <h1>Sponsors &amp; Exhibitors</h1>
@stop

@section('content')
    @include('backend.partials.alerts')

    <p class="text-muted mb-2">
        Logos appear on the homepage in the groups below, in this order.
        <strong>Drag</strong> a group by its <i class="fas fa-grip-vertical"></i> handle, or drag a logo within or between groups;
        the new order saves automatically. The section heading is edited in
        <a href="{{ route('page-content.edit', 'home.sponsors') }}">Page Content</a>.
    </p>
    <div id="orderStatus" class="small mb-3 text-muted">&nbsp;</div>

    <div id="groupList">
        @forelse ($groups as $group)
            <div class="card card-outline {{ $group->is_active ? 'card-primary' : 'card-secondary' }} sponsor-group"
                data-group-id="{{ $group->id }}">
                <div class="card-header">
                    <form action="{{ route('sponsors.groups.update', $group) }}" method="post" class="form-row align-items-center">
                        @csrf
                        <div class="col-auto">
                            <span class="drag-handle group-handle" title="Drag to reorder groups" draggable="true">
                                <i class="fas fa-grip-vertical"></i>
                            </span>
                        </div>
                        <div class="col-md-4 col-sm-6 my-1">
                            <input type="text" name="title" class="form-control form-control-sm font-weight-bold"
                                value="{{ $group->title }}" required maxlength="100" aria-label="Group title">
                        </div>
                        <div class="col-md-2 col-sm-6 my-1">
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend"><span class="input-group-text">#</span></div>
                                <input type="text" name="slug" class="form-control" value="{{ $group->slug }}"
                                    maxlength="100" title="Page anchor, e.g. /#exhibitors" aria-label="Anchor">
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6 my-1">
                            <select name="logo_size" class="form-control form-control-sm" aria-label="Logo size">
                                @foreach (\App\Models\SponsorGroup::SIZES as $size => $info)
                                    <option value="{{ $size }}" @selected($group->logo_size === $size)>{{ $info['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto my-1">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="g_active_{{ $group->id }}"
                                    name="is_active" value="1" @checked($group->is_active)>
                                <label class="custom-control-label" for="g_active_{{ $group->id }}">Show</label>
                            </div>
                        </div>
                        <div class="col-auto my-1">
                            <button type="submit" class="btn btn-sm btn-primary">Save</button>
                        </div>
                        <div class="col-auto my-1 ml-auto">
                            <button type="submit" form="delete-group-{{ $group->id }}" class="btn btn-sm btn-outline-danger">
                                Delete group
                            </button>
                        </div>
                    </form>
                    <form id="delete-group-{{ $group->id }}" action="{{ route('sponsors.groups.destroy', $group) }}"
                        method="post" onsubmit="return confirm('Delete the group &quot;{{ addslashes($group->title) }}&quot; and all {{ $group->sponsors->count() }} of its logos?');">
                        @csrf
                        @method('delete')
                    </form>
                </div>

                <div class="card-body">
                    <div class="logo-list" data-group-id="{{ $group->id }}">
                        @foreach ($group->sponsors as $sponsor)
                            <div class="logo-tile {{ $sponsor->is_active ? '' : 'is-hidden' }}" draggable="true"
                                data-id="{{ $sponsor->id }}">
                                <div class="logo-img">
                                    <img src="{{ \App\Support\Uploads::url($sponsor->logo) }}" alt="{{ $sponsor->name }}">
                                </div>
                                <div class="logo-name" title="{{ $sponsor->name }}">
                                    {{ $sponsor->name ?: 'Untitled' }}
                                    @unless ($sponsor->is_active)
                                        <span class="badge badge-secondary">hidden</span>
                                    @endunless
                                </div>
                                <div class="logo-actions">
                                    <button type="button" class="btn btn-xs btn-outline-primary edit-logo"
                                        data-action="{{ route('sponsors.logos.update', $sponsor) }}"
                                        data-name="{{ $sponsor->name }}" data-url="{{ $sponsor->url }}"
                                        data-group="{{ $sponsor->sponsor_group_id }}"
                                        data-active="{{ $sponsor->is_active ? 1 : 0 }}"
                                        data-logo="{{ \App\Support\Uploads::url($sponsor->logo) }}">Edit</button>
                                    <form action="{{ route('sponsors.logos.destroy', $sponsor) }}" method="post"
                                        class="d-inline" onsubmit="return confirm('Delete this logo?');">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="btn btn-xs btn-outline-danger">Delete</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                        <div class="logo-empty text-muted small">No logos yet – add one below or drag one here.</div>
                    </div>
                </div>

                <div class="card-footer">
                    <form action="{{ route('sponsors.logos.store') }}" method="post" enctype="multipart/form-data"
                        class="form-row align-items-end">
                        @csrf
                        <input type="hidden" name="sponsor_group_id" value="{{ $group->id }}">
                        <div class="col-md-3 my-1">
                            <label class="small mb-0">Logo image</label>
                            <input type="file" name="logo" class="form-control-file" accept=".jpg,.jpeg,.png,.webp,.gif" required>
                        </div>
                        <div class="col-md-3 my-1">
                            <label class="small mb-0">Name</label>
                            <input type="text" name="name" class="form-control form-control-sm" maxlength="150"
                                placeholder="e.g. LexisNexis">
                        </div>
                        <div class="col-md-4 my-1">
                            <label class="small mb-0">Website (optional)</label>
                            <input type="url" name="url" class="form-control form-control-sm" maxlength="500"
                                placeholder="https://...">
                        </div>
                        <div class="col-md-2 my-1">
                            <button type="submit" class="btn btn-sm btn-success btn-block">+ Add logo</button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-muted">No groups yet.</p>
        @endforelse
    </div>

    <div class="card card-success card-outline">
        <div class="card-header">
            <h3 class="card-title">Add a group</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('sponsors.groups.store') }}" method="post" class="form-row align-items-end">
                @csrf
                <div class="col-md-4 my-1">
                    <label class="small mb-0">Title</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Gold Partners" required maxlength="100">
                </div>
                <div class="col-md-3 my-1">
                    <label class="small mb-0">Anchor (optional)</label>
                    <input type="text" name="slug" class="form-control" placeholder="e.g. exhibitors" maxlength="100">
                </div>
                <div class="col-md-3 my-1">
                    <label class="small mb-0">Logo size</label>
                    <select name="logo_size" class="form-control">
                        @foreach (\App\Models\SponsorGroup::SIZES as $size => $info)
                            <option value="{{ $size }}" @selected($size === 'small')>{{ $info['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <input type="hidden" name="is_active" value="1">
                <div class="col-md-2 my-1">
                    <button type="submit" class="btn btn-success btn-block">Add group</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit logo --}}
    <div class="modal fade" id="logoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="post" enctype="multipart/form-data" id="logoForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Edit logo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3"><img id="logoPreview" src="" alt="" style="max-height: 90px; max-width: 100%"></div>
                    <div class="form-group">
                        <label>Replace image</label>
                        <input type="file" name="logo" class="form-control-file" accept=".jpg,.jpeg,.png,.webp,.gif">
                    </div>
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" id="logoName" class="form-control" maxlength="150">
                    </div>
                    <div class="form-group">
                        <label>Website</label>
                        <input type="url" name="url" id="logoUrl" class="form-control" maxlength="500" placeholder="https://...">
                    </div>
                    <div class="form-group">
                        <label>Group</label>
                        <select name="sponsor_group_id" id="logoGroup" class="form-control">
                            @foreach ($groups as $group)
                                <option value="{{ $group->id }}">{{ $group->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="logoActive" name="is_active" value="1">
                        <label class="custom-control-label" for="logoActive">Show on the website</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    <style>
        .drag-handle { cursor: grab; color: #6c757d; padding: 0 .35rem; font-size: 1.1rem; }
        .sponsor-group.dragging { opacity: .5; }
        .logo-list { display: flex; flex-wrap: wrap; gap: 12px; min-height: 70px; padding: 6px; border: 2px dashed transparent; border-radius: 6px; }
        .logo-list.drop-target { border-color: #007bff; background: #f1f7ff; }
        .logo-tile { width: 170px; border: 1px solid #dee2e6; border-radius: 6px; background: #fff; padding: 8px; cursor: grab; }
        .logo-tile.dragging { opacity: .4; }
        .logo-tile.is-hidden { opacity: .55; }
        .logo-img { height: 70px; display: flex; align-items: center; justify-content: center; }
        .logo-img img { max-height: 70px; max-width: 100%; }
        .logo-name { font-size: 12px; margin: 6px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .logo-actions { display: flex; gap: 4px; }
        .logo-list .logo-empty { display: none; align-self: center; }
        .logo-list:not(:has(.logo-tile)) .logo-empty { display: block; }
    </style>
@stop

@section('js')
    <script>
        (function() {
            var status = document.getElementById('orderStatus');
            var groupList = document.getElementById('groupList');
            var dragged = null;
            var draggedGroup = null;

            function saveOrder() {
                var payload = { groups: [], sponsors: {} };
                groupList.querySelectorAll('.sponsor-group').forEach(function(g) {
                    payload.groups.push(parseInt(g.dataset.groupId, 10));
                });
                groupList.querySelectorAll('.logo-list').forEach(function(list) {
                    payload.sponsors[list.dataset.groupId] = Array.prototype.map.call(
                        list.querySelectorAll('.logo-tile'), function(t) { return parseInt(t.dataset.id, 10); });
                });
                status.textContent = 'Saving order…';
                status.className = 'small mb-3 text-muted';
                fetch(@json(route('sponsors.reorder')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                }).then(function(r) {
                    if (!r.ok) { throw new Error(r.status); }
                    status.textContent = 'Order saved.';
                    status.className = 'small mb-3 text-success';
                }).catch(function() {
                    status.textContent = 'Could not save the new order. Reload the page and try again.';
                    status.className = 'small mb-3 text-danger';
                });
            }

            function afterElement(container, selector, x, y) {
                var items = Array.prototype.filter.call(container.querySelectorAll(selector), function(el) {
                    return !el.classList.contains('dragging');
                });
                for (var i = 0; i < items.length; i++) {
                    var box = items[i].getBoundingClientRect();
                    var sameRow = y >= box.top && y <= box.bottom;
                    if ((sameRow && x < box.left + box.width / 2) || y < box.top) { return items[i]; }
                }
                return null;
            }

            // logos
            document.querySelectorAll('.logo-tile').forEach(function(tile) {
                tile.addEventListener('dragstart', function(e) {
                    dragged = tile;
                    tile.classList.add('dragging');
                    e.dataTransfer.effectAllowed = 'move';
                    e.dataTransfer.setData('text/plain', 'logo');
                    e.stopPropagation();
                });
                tile.addEventListener('dragend', function() {
                    tile.classList.remove('dragging');
                    document.querySelectorAll('.drop-target').forEach(function(l) { l.classList.remove('drop-target'); });
                    if (dragged) { dragged = null; saveOrder(); }
                });
            });
            document.querySelectorAll('.logo-list').forEach(function(list) {
                list.addEventListener('dragover', function(e) {
                    if (!dragged) { return; }
                    e.preventDefault();
                    list.classList.add('drop-target');
                    var after = afterElement(list, '.logo-tile', e.clientX, e.clientY);
                    var empty = list.querySelector('.logo-empty');
                    list.insertBefore(dragged, after || empty);
                });
                list.addEventListener('dragleave', function() { list.classList.remove('drop-target'); });
                list.addEventListener('drop', function(e) { e.preventDefault(); });
            });

            // groups
            document.querySelectorAll('.group-handle').forEach(function(handle) {
                var card = handle.closest('.sponsor-group');
                handle.addEventListener('dragstart', function(e) {
                    draggedGroup = card;
                    card.classList.add('dragging');
                    e.dataTransfer.effectAllowed = 'move';
                    e.dataTransfer.setData('text/plain', 'group');
                    e.dataTransfer.setDragImage(card, 20, 20);
                });
                handle.addEventListener('dragend', function() {
                    card.classList.remove('dragging');
                    if (draggedGroup) { draggedGroup = null; saveOrder(); }
                });
            });
            groupList.addEventListener('dragover', function(e) {
                if (!draggedGroup) { return; }
                e.preventDefault();
                var cards = Array.prototype.filter.call(groupList.querySelectorAll('.sponsor-group'), function(c) { return c !== draggedGroup; });
                var before = null;
                for (var i = 0; i < cards.length; i++) {
                    var box = cards[i].getBoundingClientRect();
                    if (e.clientY < box.top + box.height / 2) { before = cards[i]; break; }
                }
                groupList.insertBefore(draggedGroup, before);
            });

            // edit modal
            document.querySelectorAll('.edit-logo').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    document.getElementById('logoForm').action = btn.dataset.action;
                    document.getElementById('logoPreview').src = btn.dataset.logo;
                    document.getElementById('logoName').value = btn.dataset.name || '';
                    document.getElementById('logoUrl').value = btn.dataset.url || '';
                    document.getElementById('logoGroup').value = btn.dataset.group;
                    document.getElementById('logoActive').checked = btn.dataset.active === '1';
                    $('#logoModal').modal('show');
                });
            });
        })();
    </script>
@stop
