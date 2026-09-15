@php $formId = 'menu-' . $item->id; @endphp
<tr class="{{ $child ? 'is-child' : '' }} {{ $item->is_active ? '' : 'is-hidden' }}" data-visibility-row>
    <td>
        <input form="{{ $formId }}" type="text" name="label" value="{{ $item->label }}"
            class="form-control form-control-sm" maxlength="60" required>
    </td>
    <td>
        <input form="{{ $formId }}" type="text" name="url" value="{{ $item->url }}" class="form-control form-control-sm"
            maxlength="500">
    </td>
    @if ($location === 'header')
        <td>
            @if (! $child && $item->children->count())
                <span class="small text-muted">Has {{ $item->children->count() }} sub-link(s)</span>
                <input form="{{ $formId }}" type="hidden" name="parent_id" value="">
            @else
                <select form="{{ $formId }}" name="parent_id" class="form-control form-control-sm">
                    <option value="">— Top level —</option>
                    @foreach ($parents as $parent)
                        @if ($parent->id !== $item->id)
                            <option value="{{ $parent->id }}" @selected($item->parent_id === $parent->id)>{{ $parent->label }}</option>
                        @endif
                    @endforeach
                </select>
            @endif
        </td>
    @endif
    <td>
        <input form="{{ $formId }}" type="number" name="sort_order" value="{{ $item->sort_order }}" min="0" max="9999"
            class="form-control form-control-sm">
    </td>
    <td class="text-center">
        <input form="{{ $formId }}" type="checkbox" name="new_tab" value="1" @checked($item->new_tab) aria-label="Open in new tab">
    </td>
    <td>
        @include('backend.partials.visibility-switch', [
            'action' => route('menus.toggle', $item),
            'id' => 'vis-menu-' . $item->id,
            'visible' => (bool) $item->is_active,
            'name' => $item->label,
        ])
        @if ($child && $parentHidden)
            <small class="text-muted d-block">Hidden with its dropdown</small>
        @endif
    </td>
    <td class="text-nowrap">
        <form id="{{ $formId }}" action="{{ route('menus.update', $item) }}" method="post" class="d-inline">
            @csrf
            <input type="hidden" name="location" value="{{ $location }}">
            <button type="submit" class="btn btn-sm btn-primary">Save</button>
        </form>
        <form action="{{ route('menus.destroy', $item) }}" method="post" class="d-inline"
            onsubmit="return confirm('Delete &quot;{{ addslashes($item->label) }}&quot;{{ $item->children->count() ? ' and its sub-links' : '' }}?');">
            @csrf
            @method('delete')
            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
        </form>
    </td>
</tr>
