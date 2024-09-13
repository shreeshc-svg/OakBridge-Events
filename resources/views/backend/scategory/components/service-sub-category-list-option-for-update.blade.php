<?php $dash .= '-- '; ?>
@foreach ($subcategories as $subcategory)
    @if ($subcategory->parent_id != $scategory->id)
        @if ($scategory->id != $subcategory->id)
            <option value="{{ $subcategory->id }}" @if ($scategory->parent_id == $subcategory->id) selected @endif>
                {{ $dash }}{{ $subcategory->title }}
            </option>
        @endif


        @if (count($subcategory->subcategory))
            @include('backend.scategory.components.service-sub-category-list-option-for-update', ['subcategories' => $subcategory->subcategory])
        @endif
    @endif
@endforeach
