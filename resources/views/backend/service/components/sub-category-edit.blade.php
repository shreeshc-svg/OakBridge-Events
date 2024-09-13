<?php $dash .= '-- '; ?>
@foreach ($subcategories as $subcategory)
            <option value="{{ $subcategory->id }}" @if(in_array($subcategory->id, $service->scategories->pluck('id')->toArray())) selected @endif>
                {{ $dash }}{{ $subcategory->title }}
            </option>

        @if (count($subcategory->subcategory))
            @include('backend.service.components.sub-category-edit', ['subcategories' => $subcategory->subcategory])
        @endif
@endforeach

