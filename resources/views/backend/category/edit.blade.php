@extends('adminlte::page')

@section('title', 'Edit Category')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Update Category</h1>
            <div class="pt-3">
                <a href="{{ route('category.index') }}" class="btn btn-primary ml-3"> Back</a>
            </div>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Category</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row px-3 justify-content-between">
            <div class="col-md-4">
                <form action="{{ route('category.update', $category->id) }}" method="post">
                    {{ csrf_field() }}
                    @method('PUT')
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Category name*</label>
                                    <input type="text" name="title" id="title"
                                        class="form-control @error('title') is-invalid @enderror"
                                        placeholder="Category name" value="{{ $category->title }}" />
                                    @error('title')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Category slug*</label>
                                    <input type="text" name="slug" id="slug"
                                        class="form-control @error('slug') is-invalid @enderror" placeholder="Category slug"
                                        value="{{ $category->slug }}" />
                                    @error('slug')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            {{--
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Select parent category*</label>
                                    <select type="text" name="parent_id" class="form-control">
                                        <option value="">None</option>
                                        @if ($categories)
                                            @foreach ($categories as $item)
                                                <?php $dash = ''; ?>
                                                <option value="{{$item->id}}" @if ($category->parent_id == $item->id) selected @endif>{{$item->title}}</option>
                                                @if (count($item->subcategory))
                                                    @include('backend.category.sub-category-list-option-for-update',['subcategories' => $item->subcategory])
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div> --}}

                        </div>

                    </div>
                    <div class="box-footer">
                        <button onclick="return confirm('Are you sure you want to update this item?');" type="submit"
                            class="btn btn-danger">Update</button>

                    </div>

                </form>


            </div>

        </div>
    </div>
@stop

@section('js')
    <script>
        $('#title').on("change keyup paste click", function() {
            var Text = $(this).val().trim();
            Text = Text.toLowerCase();
            Text = Text.replace(/[^a-zA-Z0-9]+/g, '-');
            $('#slug').val(Text);
        });
    </script>

    {{-- Success and error notification --}}
    <script>
        $(document).ready(function() {
            // show error message
            @if ($errors->any())
                //var errorMessage = @json($errors->any()); // Get the first validation error message
                var Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 5500
                });

                Toast.fire({
                    icon: 'error',
                    title: 'There are form validation errors. Please fix them.'
                });
            @endif

            // success message
            @if (session('success'))
                var successMessage = @json(session('success')); // Get the first sucess message
                var Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 5500
                });

                Toast.fire({
                    icon: 'success',
                    title: successMessage
                });
            @endif

        });
    </script>
@stop
