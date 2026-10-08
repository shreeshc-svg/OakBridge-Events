{{--
    Bulk actions for ticked rows. Checkboxes elsewhere on the page use form="{{ $formId }}" name="ids[]"
    and class "bulk-check"; a header checkbox with class "bulk-all" ticks them all.
    Needs: $formId, $action (url), $years, $noun ("speaker"). Optional: $canCopy.
--}}
<form id="{{ $formId }}" action="{{ $action }}" method="post" class="bulk-bar card card-body py-2 mb-3" data-noun="{{ $noun }}">
    @csrf
    <div class="form-row align-items-center">
        <div class="col-auto my-1">
            <span class="bulk-count badge badge-secondary">0 selected</span>
        </div>
        <div class="col-auto my-1">
            <select name="edition_id" class="form-control form-control-sm" aria-label="Year to move or copy to">
                <option value="">Year…</option>
                @foreach ($years as $y)
                    <option value="{{ $y->id }}">{{ $y->year }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto my-1">
            <button type="submit" name="action" value="move" class="btn btn-sm btn-outline-primary bulk-btn" disabled>
                <i class="fas fa-fw fa-share"></i> Move to year
            </button>
            @if ($canCopy ?? false)
                <button type="submit" name="action" value="copy" class="btn btn-sm btn-outline-primary bulk-btn" disabled
                    title="Keeps the original and adds a copy to the chosen year">
                    <i class="fas fa-fw fa-copy"></i> Copy to year
                </button>
            @endif
            <button type="submit" name="action" value="delete" class="btn btn-sm btn-outline-danger bulk-btn" disabled>
                <i class="fas fa-fw fa-trash"></i> Delete
            </button>
        </div>
    </div>
</form>
