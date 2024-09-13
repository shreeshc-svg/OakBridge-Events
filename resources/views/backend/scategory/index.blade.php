@extends('adminlte::page')

@section('title', 'Service Categories')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Category</h1>
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



        @if (session()->has('success'))
            <div class="alert alert-dismissable alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>
                    {!! session()->get('success') !!}
                </strong>
            </div>
        @endif
        <div class="row px-3 pt-3 justify-content-between">
            <div class="col-md-4 ">
                <h5>Add New Category
                </h5>
                <form role="form" method="post">
                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Category name*</label>
                                    <input type="text" name="title" id="title"
                                        class="form-control @error('title') is-invalid @enderror"
                                        placeholder="Category Name" value="{{ old('title') }}" />
                                    @error('title')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="slug" class="form-label bg-light">Slug <small>Unique url of the
                                            category</small></label>
                                    <input type="text" name="slug"
                                        class="form-control bg-light @error('slug') is-invalid @enderror" id="slug">
                                    @error('slug')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>


                            {{-- <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Select parent category*</label>
                                    <select type="text" name="parent_id" class="form-control">
                                        <option value="">None</option>
                                        @if ($scategories)
                                            @foreach ($scategories as $category)
                                                <?php $dash = ''; ?>
                                                <option value="{{$category->id}}">{{$category->title}}</option>
                                                @if (count($category->subcategory))
                                                    @include('backend.scategory.components.serviceSubcategory',['subcategories' => $category->subcategory])
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div> --}}

                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Create</button>

                    </div>
                </form>


            </div>
            <div class="col-md-8 pt-4 pl-lg-5">
                <div class="card p-2">

                    <div id="" class="card-body p-0">
                        <table id="mytable" class="table table-striped projects">
                            <thead>
                                <tr>
                                    <th style="width: 1%">
                                        #
                                    </th>
                                    <th style="width: 20%">
                                        Name
                                    </th>
                                    <th style="width: 20%">
                                        Slug
                                    </th>
                                    <th style="width: 30%">
                                        Parent Category
                                    </th>
                                    <th style="width: 29%">Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $_SESSION['i'] = 0; ?>

                                @foreach ($scategories as $category)
                                    <?php $_SESSION['i'] = $_SESSION['i'] + 1; ?>
                                    <tr>
                                        <?php $dash = ''; ?>
                                        <td>{{ $_SESSION['i'] }}</td>
                                        <td>
                                            <a>
                                                {{ $category->title }}
                                            </a>

                                        </td>
                                        <td>
                                            <a>{{ $category->slug }}</a>
                                        </td>
                                        <td>
                                            @if (isset($category->parent_id))
                                                {{ $category->subcategory->title }}
                                            @else
                                                None
                                            @endif
                                        </td>

                                        <td class="project-actions text-right d-flex">

                                            <div>
                                                <a class="btn btn-info btn-sm"
                                                    href="{{ route('scategory.edit', $category->id) }}">
                                                    <i class="fas fa-pencil-alt">
                                                    </i>
                                                    Edit
                                                </a>
                                            </div>
                                            <div>
                                                {{-- @if ($category->posts->count())
                                                    <button
                                                        onclick="return confirm('Are you sure you want to delete this item?');"
                                                        class="btn btn-danger btn-sm ml-2 disabled">
                                                        <i class="fas fa-trash"></i>
                                                        Delete
                                                        </a>
                                                    </button>
                                                @else --}}
                                                <form action="{{ route('scategory.destroy', $category->id) }}"
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
                                    @if ($category->subcategory)
                                        @include('backend.scategory.components.serviceSubCategoryList', [
                                            'subcategories' => $category->subcategory,
                                        ])
                                    @endif
                                @endforeach
                                <?php unset($_SESSION['i']); ?>
                            </tbody>
                        </table>
                        {{-- <div class="p-2">
                            {{ $categories->links() }}
                        </div> --}}
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $(".alert").delay(5000).slideUp(300);
        });
    </script>

    <script>
        $('#title').on("change keyup paste click", function() {
            var Text = $(this).val().trim();
            Text = Text.toLowerCase();
            Text = Text.replace(/[^a-zA-Z0-9]+/g, '-');
            $('#slug').val(Text);
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#mytable').DataTable();
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
