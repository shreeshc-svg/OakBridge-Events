<?php $dash.='-- '; ?>
@foreach($subcategories as $subcategory)
    <?php $_SESSION['i']=$_SESSION['i']+1; ?>
    <tr>
        <td>{{$_SESSION['i']}}</td>
        <td>{{$dash}}{{$subcategory->title}}</td>
        <td>{{$subcategory->slug}}</td>
        {{-- <td>  {{ $category->posts->count() }}</td> --}}
        <td>{{$subcategory->parent->title}}</td>
        <td class="project-actions text-right d-flex">
            <div>
                <a class="btn btn-info btn-sm"
                    href="{{ route('category.edit', $subcategory->id) }}">
                    <i class="fas fa-pencil-alt">
                    </i>
                    Edit
                </a>
            </div>
            <div>
                <form action="{{ route('category.destroy', $subcategory->id) }}"
                    method="POST">
                    @csrf
                    @method('DELETE')
                    <button
                        onclick="return confirm('Category cannot be delted - Post attached');"
                        class="btn btn-danger btn-sm ml-2">
                        <i class="fas fa-trash"></i>
                        Delete
                        </a>
                    </button>
                </form>

            </div>
        </td>
    </tr>
    @if(count($subcategory->subcategory))
        @include('backend.category.sub-category-list',['subcategories' => $subcategory->subcategory])
    @endif
@endforeach
