@extends('adminlte::page')

@section('title', $def['page'] . ' – ' . $def['label'])

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1>{{ $def['page'] }} <small class="text-muted">› {{ $def['label'] }}</small></h1>
        <a href="{{ route('page-content.index') }}" class="btn btn-outline-secondary btn-sm">&larr; All sections</a>
    </div>
@stop

@section('content')
    @include('backend.partials.alerts')

    @if (!empty($def['hint']))
        <p class="text-muted">{{ $def['hint'] }}</p>
    @endif

    @if (\App\Support\SiteSections::find($key))
        <div class="callout callout-info py-2 d-flex align-items-center flex-wrap">
            <span class="mr-3">Show this section on the site:</span>
            @include('backend.partials.visibility-switch', [
                'action' => route('page-content.visibility', $key),
                'id' => 'vis-section',
                'visible' => \App\Support\SiteSections::isVisible($key),
                'name' => $def['label'],
            ])
            <small class="text-muted ml-3">Saves straight away. Hidden sections keep their text.</small>
        </div>
    @endif

    <form action="{{ route('page-content.update', $key) }}" method="post" enctype="multipart/form-data" id="contentForm">
        @csrf
        <div class="card card-primary card-outline">
            <div class="card-body">
                @foreach ($def['fields'] as $name => $field)
                    @php $value = old($name, $values[$name] ?? ''); @endphp
                    <div class="form-group">
                        @unless ($field['type'] === 'toggle')
                            <label for="f_{{ $name }}">{{ $field['label'] }}</label>
                        @endunless

                        @switch($field['type'])
                            @case('toggle')
                                <div class="custom-control custom-switch">
                                    <input type="hidden" name="{{ $name }}" value="0">
                                    <input type="checkbox" class="custom-control-input" id="f_{{ $name }}"
                                        name="{{ $name }}" value="1" {{ old($name, $values[$name] ?? false) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="f_{{ $name }}">
                                        <strong>{{ $field['label'] }}</strong>
                                    </label>
                                </div>
                            @break

                            @case('text')
                                <input type="text" class="form-control @error($name) is-invalid @enderror" id="f_{{ $name }}"
                                    name="{{ $name }}" value="{{ $value }}" maxlength="255">
                            @break

                            @case('textarea')
                                <textarea class="form-control @error($name) is-invalid @enderror" id="f_{{ $name }}" name="{{ $name }}"
                                    rows="4">{{ $value }}</textarea>
                            @break

                            @case('richtext')
                                <textarea class="form-control rich-editor" id="f_{{ $name }}" name="{{ $name }}" rows="12">{{ $value }}</textarea>
                            @break

                            @case('image')
                                @if (!empty($values[$name]))
                                    <div class="mb-2">
                                        <img src="{{ \App\Support\Uploads::url($values[$name]) }}" alt="" class="img-thumbnail"
                                            style="max-height: 180px">
                                    </div>
                                @endif
                                <input type="file" class="form-control-file" id="f_{{ $name }}" name="{{ $name }}"
                                    accept=".jpg,.jpeg,.png,.webp,.gif">
                                @if (str_starts_with((string) ($values[$name] ?? ''), 'public/uploads/'))
                                    <div class="custom-control custom-checkbox mt-2">
                                        <input type="checkbox" class="custom-control-input" id="reset_{{ $name }}"
                                            name="reset_{{ $name }}" value="1">
                                        <label class="custom-control-label" for="reset_{{ $name }}">Go back to the original image</label>
                                    </div>
                                @endif
                            @break

                            @case('file')
                                @if (!empty($values[$name]))
                                    <div class="mb-2">
                                        Current file:
                                        <a href="{{ \App\Support\Uploads::url($values[$name]) }}" target="_blank">{{ basename($values[$name]) }}</a>
                                    </div>
                                @endif
                                <input type="file" class="form-control-file" id="f_{{ $name }}" name="{{ $name }}" accept=".pdf">
                                <small class="form-text text-muted">PDF, up to 20 MB.</small>
                                @if (!empty($values[$name]))
                                    <div class="custom-control custom-checkbox mt-2">
                                        <input type="checkbox" class="custom-control-input" id="clear_{{ $name }}"
                                            name="clear_{{ $name }}" value="1">
                                        <label class="custom-control-label" for="clear_{{ $name }}">
                                            Remove this file &ndash; there is nothing to download yet
                                        </label>
                                    </div>
                                @endif
                            @break
                        @endswitch

                        @if (!empty($field['help']))
                            <small class="form-text text-muted">{{ $field['help'] }}</small>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        @if (isset($def['items']))
            @php
                $items = old('items', $values['items'] ?? []);
                $itemFields = $def['items']['fields'];
            @endphp
            <div class="card card-primary card-outline">
                <div class="card-header d-flex align-items-center">
                    <h3 class="card-title mb-0">{{ $def['items']['label'] }}</h3>
                    <small class="text-muted ml-2">up to {{ $def['items']['max'] }}</small>
                </div>
                <div class="card-body">
                    <div id="itemList">
                        @foreach ($items as $i => $item)
                            @include('backend.page-content.item-row', ['i' => $i, 'item' => $item, 'itemFields' => $itemFields])
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-outline-primary" id="addItem"
                        data-max="{{ $def['items']['max'] }}">+ Add</button>
                    <template id="itemTemplate">
                        @include('backend.page-content.item-row', ['i' => '__INDEX__', 'item' => [], 'itemFields' => $itemFields])
                    </template>
                </div>
            </div>
        @endif

        <div class="mb-4">
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="{{ route('home') }}" target="_blank" class="btn btn-outline-secondary">View site</a>
        </div>
    </form>

    @if ($customised)
        <form action="{{ route('page-content.reset', $key) }}" method="post" class="mb-4"
            onsubmit="return confirm('Discard your changes to this section and show the original content again?');">
            @csrf
            <button type="submit" class="btn btn-link text-danger p-0">Reset this section to the original content</button>
        </form>
    @endif
@stop

@section('css')
    <style>
        .item-row { border: 1px solid #dee2e6; border-radius: .25rem; padding: .75rem .75rem 0; margin-bottom: .75rem; background: #fafafa; }
        .item-row .item-tools { white-space: nowrap; }
    </style>
@stop

@section('js')
    @if (\App\Support\SiteSections::find($key))
        @include('backend.partials.visibility-switch-js')
    @endif
    @include('backend.partials.rich-editor')
    <script>
        (function() {
            var list = document.getElementById('itemList');
            var addBtn = document.getElementById('addItem');
            if (!list || !addBtn) { return; }
            var tpl = document.getElementById('itemTemplate').innerHTML;
            var counter = list.children.length + 1000;

            function renumber() {
                // keep submitted order = visual order
                Array.prototype.forEach.call(list.children, function(row, index) {
                    row.querySelectorAll('[data-field]').forEach(function(input) {
                        input.name = 'items[' + index + '][' + input.dataset.field + ']';
                    });
                    var n = row.querySelector('.item-number');
                    if (n) { n.textContent = '#' + (index + 1); }
                });
                addBtn.disabled = list.children.length >= parseInt(addBtn.dataset.max, 10);
            }

            addBtn.addEventListener('click', function() {
                var wrapper = document.createElement('div');
                wrapper.innerHTML = tpl.replace(/__INDEX__/g, counter++).trim();
                list.appendChild(wrapper.firstElementChild);
                renumber();
            });

            list.addEventListener('click', function(e) {
                var btn = e.target.closest('button[data-action]');
                if (!btn) { return; }
                var row = btn.closest('.item-row');
                if (btn.dataset.action === 'remove') { row.remove(); }
                if (btn.dataset.action === 'up' && row.previousElementSibling) { list.insertBefore(row, row.previousElementSibling); }
                if (btn.dataset.action === 'down' && row.nextElementSibling) { list.insertBefore(row.nextElementSibling, row); }
                renumber();
            });

            renumber();
        })();
    </script>
@stop
