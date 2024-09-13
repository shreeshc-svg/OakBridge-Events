@extends('adminlte::page')

@section('title', 'Edit tags')

@section('content_header')
    <h1>Update Tag</h1>
    <div class="pt-3">
        <a href="{{ route('tag.index') }}" class="btn btn-primary ml-3"> Back</a>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row px-3 justify-content-between">
            <div class="col-md-4">
                <form action="{{ route('tag.update', $tag->id) }}" method="post">
                    {{ csrf_field() }}
                    @method('PUT')
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Tag name<span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
                                        placeholder="tag name" value="{{ $tag->title }}" />
                                        @error('title')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Tag slug<span class="text-danger">*</span></label>
                                    <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror"
                                        placeholder="tag slug" value="{{ $tag->slug }}" />
                                        @error('slug')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                </div>
                            </div>

                        </div>

                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-danger">Update</button>


                    </div>

                </form>


            </div>

        </div>
    </div>
@stop

@section('js')
    <script>
        $('#title').on("change keyup paste click", function() {
            var Text = $(this).val();
            Text = Text.toLowerCase();
            Text = Text.replace(/[^a-zA-Z0-9]+/g, '-');
            $('#slug').val(Text);
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
@stop
