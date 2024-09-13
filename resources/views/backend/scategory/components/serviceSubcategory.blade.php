<?php $dash.='--'?>
@foreach ($subcategories as $subcategory)
    <option value="{{$subcategory->id}}"  {{ in_array($category->id, old('scategory', [])) ? 'selected' : '' }}>{{$dash}}{{$subcategory->title}}</option>

    @if (count($subcategory->subcategory))
        @include('backend.scategory.components.serviceSubcategory',['subcategories' => $subcategory->subcategory])
    @endif
@endforeach
