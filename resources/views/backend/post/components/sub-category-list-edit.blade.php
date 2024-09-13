<?php $dash .= '-- '; ?>
@foreach ($subcategories as $subcategory)

            <option value="{{ $subcategory->id }}" @if(in_array($subcategory->id, $post->categories->pluck('id')->toArray())) selected @endif>
                {{ $dash }}{{ $subcategory->title }}
            </option>

        @if (count($subcategory->subcategory))
            @include('backend.post.components.sub-category-list-edit', ['subcategories' => $subcategory->subcategory])
        @endif
@endforeach
