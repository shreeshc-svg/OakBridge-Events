<div class="item-row">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <strong class="item-number"></strong>
        <div class="item-tools">
            <button type="button" class="btn btn-xs btn-outline-secondary" data-action="up" title="Move up">&uarr;</button>
            <button type="button" class="btn btn-xs btn-outline-secondary" data-action="down" title="Move down">&darr;</button>
            <button type="button" class="btn btn-xs btn-outline-danger" data-action="remove">Remove</button>
        </div>
    </div>
    <div class="form-row">
        @foreach ($itemFields as $name => $field)
            <div class="form-group {{ $field['type'] === 'textarea' ? 'col-12' : (count($itemFields) === 1 ? 'col-12' : 'col-md-6') }}">
                <label class="small mb-1">{{ $field['label'] }}</label>
                @if ($field['type'] === 'textarea')
                    <textarea class="form-control form-control-sm" rows="2" data-field="{{ $name }}"
                        name="items[{{ $i }}][{{ $name }}]">{{ $item[$name] ?? '' }}</textarea>
                @else
                    <input type="text" class="form-control form-control-sm" data-field="{{ $name }}"
                        name="items[{{ $i }}][{{ $name }}]" value="{{ $item[$name] ?? '' }}">
                @endif
                @if (!empty($field['help']))
                    <small class="form-text text-muted">{{ $field['help'] }}</small>
                @endif
            </div>
        @endforeach
    </div>
</div>
