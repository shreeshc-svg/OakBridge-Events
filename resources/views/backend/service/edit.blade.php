@extends('adminlte::page')

@section('title', 'Edit Event')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Edit Event</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Edit Event</li>
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
        @if (count($errors) > 0)
            <div class="alert alert-dismissable alert-danger mt-3">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>Whoops!</strong> There were some problems with your input.<br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('service.update', $service->id) }}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            @method('PUT')
            <div class="row ">
                <div class="col-md-8">
                    <div class="card card-light">
                        <div class="card-header">
                            <h3 class="card-title">Edit Event
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus" aria-hidden="true">
                                    </i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="inputStatus">Title
                                </label>
                                <input class="form-control @error('title') is-invalid @enderror" type="text"
                                    id="title" name="title" placeholder="Title here.." value="{{ $service->title }}">
                                @error('title')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="inputStatus">Slug
                                </label>
                                <small>&nbsp;&nbsp;Unique url of the Service
                                </small>
                                <input class="form-control bg-light @error('slug') is-invalid @enderror" type="text"
                                    id="slug" name="slug" placeholder="slug here.." value="{{ $service->slug }}">
                                @error('slug')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="">Eventd Description
                                </label>
                                <textarea style="height: 600px;" id="summernote" name="body" value="{{ $service->body }}" name="body" required />{{ $service->body }}</textarea>
                                @error('body')
                                    <li class="text-danger">{{ $message }}</li>
                                @enderror
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    {{-- <div class="card card-light">
                        <div class="card-header">
                            <h3 class="card-title">Video Link
                            </h3>
                            <small>&nbsp; &nbsp;Youtube video id only, Ex: <span
                                    class="text-danger text-underline">LMmuChXra_M</span></small>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus" aria-hidden="true">
                                    </i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <input class="form-control" type="text" name="video" placeholder="Youtube video link"
                                    value="{{ $service->video }}">
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div> --}}
                    <div class="card card-light">
                        <div class="card-header">
                            <h3 class="card-title">Excerpt
                            </h3>
                            <small>&nbsp;&nbsp;Small Description of the Service
                            </small>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus" aria-hidden="true">
                                    </i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <textarea class="form-control" name="excerpt" id="" value="{{ $service->excerpt }}" cols="30"
                                    rows="5">{{ $service->excerpt }}</textarea>
                            </div>
                        </div>

                    </div>
                    <!-- /.card -->
                    {{-- <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Timeline</h3>
                            <small>&nbsp;&nbsp;Timeline entries</small>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body pt-3 pb-0">
                            <div>
                                <label>From</label>
                                <input type="text" class="form-control" id="timelineFrom" />
                            </div>
                            <div>
                                <label>To</label>
                                <input type="text" class="form-control" id="timelineTo" />
                            </div>
                            <div>
                                <label>Title</label>
                                <input type="text" class="form-control" id="timelineTitle" />
                            </div>
                            <div>
                                <label>Subheadline</label>
                                <input type="text" class="form-control" id="timelineSubheadline" />
                            </div>
                            <div>
                                <label>Body</label>
                                <textarea class="form-control" rows="4" id="timelineBody"></textarea>
                            </div>
                            <button class="btn btn-primary btn-sm my-3" type="button" id="addTimeline">Add Timeline
                                Entry</button>

                            <div id="timelineList">
                                <!-- Existing timeline entries, if validation fails -->
                                @if (old('timeline'))
                                    @foreach (json_decode(old('timeline')) as $key => $timeline)
                                        <div class="timeline-item">
                                            <strong>{{ $timeline->title }}</strong>
                                            <p>{{ $timeline->body }}</p>
                                            <button type="button" class="removeTimeline btn btn-danger btn-sm mb-3"
                                                data-index="{{ $key }}">Remove</button>
                                        </div>
                                        <hr>
                                    @endforeach
                                @endif
                            </div>

                            <!-- Hidden input to store the final JSON object -->
                            <input type="hidden" name="timeline" id="timelineData" />
                        </div>
                    </div> --}}


                    {{-- <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Timeline</h3>
                            <small>&nbsp;&nbsp;Timeline entries</small>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body pt-3 pb-0">
                            <div>
                                <label>From</label>
                                <input type="text" class="form-control" id="timelineFrom" />
                            </div>
                            <div>
                                <label>To</label>
                                <input type="text" class="form-control" id="timelineTo" />
                            </div>
                            <div>
                                <label>Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="timelineTitle" />
                            </div>
                            <div>
                                <label>Subheadline</label>
                                <input type="text" class="form-control" id="timelineSubheadline" />
                            </div>
                            <div>
                                <label>Body <span class="text-danger">*</span></label>
                                <textarea class="form-control" rows="4" id="timelineBody"></textarea>
                            </div>
                            <button class="btn btn-primary btn-sm my-3" type="button" id="addTimeline">Add Timeline
                                Entry</button>

                            <div id="timelineList">
                                <!-- Existing timeline entries, if validation fails -->
                                @if (old('timeline'))
                                    @foreach (json_decode(old('timeline')) as $key => $timeline)
                                        <div class="timeline-item">
                                            <strong>{{ $timeline->title }}</strong>
                                            <em>{{ $timeline->subheadline }}</em>
                                            <p><strong>From:</strong> {{ $timeline->from }}</p>
                                            <p><strong>To:</strong> {{ $timeline->to }}</p>
                                            <p><strong>Body:</strong> {{ $timeline->body }}</p>
                                            <button type="button" class="removeTimeline btn btn-danger btn-sm mb-3"
                                                data-index="{{ $key }}">Remove</button>
                                        </div>
                                        <hr>
                                    @endforeach
                                @endif
                            </div>

                            <!-- Hidden input to store the final JSON object -->
                            <input type="hidden" name="timeline" id="timelineData" />
                        </div>
                    </div> --}}


                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Timeline</h3>
                            <small>&nbsp;&nbsp;Timeline entries</small>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body pt-3 pb-0">
                            <div class="mb-3">
                                <label class="mb-0">From</label>
                                <input type="text" class="form-control" id="timelineFrom" />
                            </div>
                            <div class="mb-3">
                                <label class="mb-0">To</label>
                                <input type="text" class="form-control" id="timelineTo" />
                            </div>
                            <div class="mb-3">
                                <label class="mb-0">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="timelineTitle" />
                            </div>
                            <div class="mb-3">
                                <label class="mb-0">Subheadline</label>
                                <input type="text" class="form-control" id="timelineSubheadline" />
                            </div>
                            <div class="mb-2">
                                <label class="mb-0">Body <span class="text-danger">*</span></label>
                                <textarea class="form-control" rows="4" id="timelineBody"></textarea>
                            </div>
                            <button class="btn btn-primary btn-sm my-3" type="button" id="addTimeline">Add Timeline
                                Entry</button>

                            <div id="timelineList">
                                <!-- Existing timeline entries, if validation fails -->
                                @if (old('timeline'))
                                    @foreach (json_decode(old('timeline')) as $key => $timeline)
                                        <div class="timeline-item">
                                            <strong>{{ $timeline->title }}</strong>
                                            <em>{{ $timeline->subheadline }}</em>
                                            <p><strong>From:</strong> {{ $timeline->from }}</p>
                                            <p><strong>To:</strong> {{ $timeline->to }}</p>
                                            <p><strong>Body:</strong> {{ $timeline->body }}</p>
                                            <button type="button" class="removeTimeline btn btn-danger btn-sm mb-3"
                                                data-index="{{ $key }}">Remove</button>
                                            <button type="button" class="editTimeline btn btn-primary btn-sm mb-3 ml-2"
                                                data-index="{{ $key }}">Edit</button>
                                        </div>
                                        <hr>
                                    @endforeach
                                @endif
                            </div>

                            <!-- Hidden input to store the final JSON object -->
                            <input type="hidden" name="timeline" id="timelineData" />
                        </div>
                    </div>

                    <!-- Modal for Editing Timeline Entry -->
                    <!-- Modal for Editing Timeline Entry -->
                    <div class="modal fade" id="editTimelineModal" tabindex="-1"
                        aria-labelledby="editTimelineModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header p-2">
                                    <h5 class="modal-title" id="editTimelineModalLabel">Edit Timeline Entry</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div>
                                        <label>From</label>
                                        <input type="text" class="form-control" id="editTimelineFrom" />
                                    </div>
                                    <div>
                                        <label>To</label>
                                        <input type="text" class="form-control" id="editTimelineTo" />
                                    </div>
                                    <div>
                                        <label>Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="editTimelineTitle" />
                                    </div>
                                    <div>
                                        <label>Subheadline</label>
                                        <input type="text" class="form-control" id="editTimelineSubheadline" />
                                    </div>
                                    <div>
                                        <label>Body <span class="text-danger">*</span></label>
                                        <textarea class="form-control" rows="4" id="editTimelineBody"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" id="updateTimeline">Update</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- seo --}}
                    <div class="card card-light">
                        <div class="card-header">
                            <h3 class="card-title">SEO
                            </h3>
                            <small>&nbsp;&nbsp;Search engine details
                            </small>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                    title="Collapse">
                                    <i class="fas fa-minus" aria-hidden="true">
                                    </i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body pt-3 pb-0">
                            <div class="form-group">
                                <label for="">SEO Title
                                </label>
                                <input placeholder="Service title here for seo..." type="text" class="form-control"
                                    name="meta_title" id="" value="{{ $service->meta_title }}">
                            </div>
                        </div>
                        <div class="card-body  pt-0 pb-0">
                            <div class="form-group">
                                <label for="">SEO Description
                                </label>
                                <textarea placeholder="Service description here for seo..." class="form-control" name="meta_description"
                                    id="" cols="0" rows="4" value="{{ $service->meta_description }}">{{ $service->meta_description }}</textarea>
                            </div>
                        </div>
                        <div class="card-body pt-0 pb-0">
                            <div class="form-group">
                                <label for="">SEO Keywords
                                </label>
                                <input type="text" class="form-control" placeholder="keyword1, keyword2, keyword3"
                                    name="meta_keyword" id="" value="{{ $service->meta_keyword }}">
                            </div>
                        </div>
                        {{-- <div class="card-body pt-0">
                            <div class="form-group select2-primary">
                                <label for="">SEO Tags
                                </label>
                                <select id="tags" name="tag[]" class="select2" multiple="multiple"
                                    data-placeholder="Search Tags" style="width: 100%;">
                                    @foreach ($tags as $tag)
                                        <option value="{{ $tag->id }}"
                                            {{ $service->tags->contains('id', $tag->id) ? 'selected' : '' }}>
                                            {{ $tag->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div> --}}
                        <!-- /.card-body -->
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="sticky-top">
                        <div class="card card-info sticky-bottom">
                            <div class="card-header">
                                <h3 class="card-title">Event Details
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                        title="Collapse">
                                        <i class="fas fa-minus" aria-hidden="true">
                                        </i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body pb-0">
                                {{-- <div class="form-group select2-dark">
                                    <label>Category
                                    </label>
                                    <small>&nbsp;&nbsp;</small>



                                    <select id="category" name="scategory[]" class="select2" multiple="multiple"
                                        data-placeholder="Search Category" style="width: 100%;" required>
                                        <option value="">None</option>
                                        @if ($scategories)
                                            @foreach ($scategories as $item)
                                                <?php $dash = ''; ?>
                                                <option value="{{ $item->id }}"
                                                    @if (in_array($item->id, $service->scategories->pluck('id')->toArray())) selected @endif>
                                                    {{ $item->title }}
                                                </option>
                                                @if (count($item->subcategory))
                                                    @include(
                                                        'backend.service.components.sub-category-edit',
                                                        ['subcategories' => $item->subcategory]
                                                    )
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div> --}}

                                <div class="form-group">
                                    <label for="event_datetime">Event Date and Time</label>
                                    <input type="datetime-local" id="event_datetime" name="date" class="form-control"
                                        value="{{ \Carbon\Carbon::parse($service->date)->format('Y-m-d\TH:i') }}"
                                        required>
                                </div>

                                <div class="form-group">
                                    <label for="inputStatus">Status
                                    </label>
                                    <select required="required" name="published" id="inputStatus"
                                        class="form-control custom-select">
                                        <option selected="" disabled="" value="">Select Option
                                        </option>&gt;
                                        <option @if ($service->published == true) selected @endif value="1">
                                            PUBLISHED
                                        </option>
                                        <option @if ($service->published == false) selected @endif value="0"> DRAFT
                                        </option>
                                    </select>
                                </div>
                                {{-- <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="disable_comment"
                                            name="disable_comment" value="1"
                                            @if ($service->disable_comment == true) checked @endif>
                                        <label class="custom-control-label" for="disable_comment">Disable Comments
                                        </label>
                                        <small>&nbsp;&nbsp;default is enabled
                                        </small>
                                    </div>
                                </div> --}}
                                {{-- <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="featured"
                                            name="featured" value="1"
                                            @if ($service->featured == true) checked @endif>
                                        <label class="custom-control-label" for="featured">Featured
                                        </label>
                                        <br>
                                        <small>Featured will be shown on home page on priorty</small>
                                    </div>
                                </div> --}}
                                <div class="form-group pt-0 pb-0 text-right">
                                    <button onclick="return confirm('Are you sure you want to update this Service?');"
                                        type="submit" class="btn btn-danger">Update
                                    </button>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <div class="card card-info ">
                            <div class="card-header">
                                <h3 class="card-title">Featured Image
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                        title="Collapse">
                                        <i class="fas fa-minus" aria-hidden="true">
                                        </i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body pt-0 pb-0">
                                <div class="form-group">
                                    <small class="text-red">&nbsp;&nbsp;Note: size: Width-1200px Height: 800px
                                    </small>
                                    <input name="image" accept="image/*" type="file" id="imgInp">
                                    @if ($service->image)
                                        <img style="width: 175px; margin-top:10px; border:1px solid black;" id="blah"
                                            src="{{ asset('public/uploads/images/service/' . $service->image) }}"
                                            alt="your image">
                                    @else
                                        <img style="width: 175px; margin-top:10px; border:1px solid black;" id="blah"
                                            src="{{ asset('public/uploads/images/no-image.jpg') }}" alt="your image">
                                    @endif
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </form>
    </div>

@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    {{-- <style>
        /* summer note */
        .modal-header .close,
        .modal-header .mailbox-attachment-close {
            padding: 0rem;
            margin: 0 auto;
        }

        .modal-header {
            display: -ms-flexbox;
            display: block;
            -ms-flex-align: start;
            align-items: flex-start;
            -ms-flex-pack: justify;
            justify-content: space-between;
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
            border-top-left-radius: calc(0.3rem - 1px);
            border-top-right-radius: calc(0.3rem - 1px);
        }
    </style> --}}

@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

    {{-- summer note --}}
    <script>
        $(document).ready(function() {
            $('#summernote').summernote({
                height: 400,

                callbacks: {
                    onImageUpload: function(files) {
                        uploadImage(files[0]);
                    },
                    onMediaDelete: function(target) {
                        deleteImage(target[0].src);
                        if (target[0].nodeName === 'VIDEO') {
                            // Check if the deleted element is a video
                            target.remove(); // Remove the video element
                        }
                    },

                }
            });

            function uploadImage(file) {
                let formData = new FormData();
                formData.append('image', file);

                $.ajax({
                    url: '{{ route('summer.upload.image') }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        let imageUrl = response.url;
                        $('#summernote').summernote('editor.insertImage', imageUrl);
                    },
                    error: function(error) {
                        console.error(error);
                    }
                });
            }

            function deleteImage(imageSrc) {
                console.log('Deleting image with source URL:', imageSrc);

                $.ajax({
                    url: '{{ route('summer.delete.image') }}',
                    type: 'POST',
                    data: {
                        imageSrc: imageSrc
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log(response.message);
                    },
                    error: function(error) {
                        console.error(error);
                    }
                });
            }

        });
    </script>
    {{-- view image while uploading --}}
    <script>
        imgInp.onchange = evt => {
            const [file] = imgInp.files
            if (file) {
                blah.src = URL.createObjectURL(file)
            }
        }
    </script>
    {{-- create live slug --}}
    <script>
        $('#title').on("change keyup paste click", function() {
            var Text = $(this).val().trim();
            Text = Text.toLowerCase();
            Text = Text.replace(/[^a-zA-Z0-9]+/g, '-');
            $('#slug').val(Text);
        });
    </script>
    <script>
        // In your Javascript (external .js resource or <script> tag)
        $(document).ready(function() {
            $('#tags').select2();
        });
    </script>

    {{-- for catgory  --}}
    <script>
        // In your Javascript (external .js resource or <script> tag)
        $(document).ready(function() {
            $('#category').select2({
                allowClear: true,
            });
        });
    </script>


    {{-- for auto hide alert message --}}
    <script>
        $(document).ready(function() {
            $(".alert").delay(6000).slideUp(300);
        });
    </script>



    {{-- <script>
        let timelineArray = [];

        // Populate the timelineArray from old data in the hidden input
        @if (old('timeline'))
            timelineArray = @json(json_decode(old('timeline')));
        @endif

        // Display old timeline data
        function displayTimeline() {
            $('#timelineList').empty(); // Clear existing timeline items
            timelineArray.forEach((timeline, idx) => {
                let timelineHtml = `<div class="timeline-item card p-2">
            <strong>${timeline.title || 'No Title'}</strong><br>
            <em>${timeline.subheadline || 'No Subheadline'}</em><br>
            <p><strong>From:</strong> ${timeline.from || 'No From Time'}</p>
            <p><strong>To:</strong> ${timeline.to || 'No To Time'}</p>
            <p><strong>Body:</strong> ${timeline.body || 'No Body'}</p>
            <button type="button" class="removeTimeline btn btn-danger btn-sm mb-1 ml-auto" data-index="${idx}">Remove</button>
        </div>`;
                $('#timelineList').append(timelineHtml);
            });

            // Update hidden input with JSON
            $('#timelineData').val(JSON.stringify(timelineArray));
        }

        // Initial display of timeline entries if there are old values
        displayTimeline();

        // Add new timeline entry to array and show in real-time
        $('#addTimeline').on('click', function() {
            let from = $('#timelineFrom').val() || null;
            let to = $('#timelineTo').val() || null;
            let title = $('#timelineTitle').val() || null;
            let subheadline = $('#timelineSubheadline').val() || null;
            let body = $('#timelineBody').val() || null;

            // Ensure that at least one of the fields is filled out before adding
            if (from || to || title || subheadline || body) {
                let timeline = {
                    from: from,
                    to: to,
                    title: title,
                    subheadline: subheadline,
                    body: body
                };
                timelineArray.push(timeline);

                // Clear input fields
                $('#timelineFrom').val('');
                $('#timelineTo').val('');
                $('#timelineTitle').val('');
                $('#timelineSubheadline').val('');
                $('#timelineBody').val('');

                // Rebuild the timeline list
                displayTimeline();
            }
        });

        // Remove timeline entry from the array and UI
        $(document).on('click', '.removeTimeline', function() {
            let index = $(this).data('index');
            timelineArray.splice(index, 1); // Remove from array

            // Rebuild timeline list
            displayTimeline();
        });

        // Submit form (AJAX or regular submission)
        $('#timelineForm').on('submit', function(e) {
            // Optional: Perform validation or additional logic here
        });
    </script>

    <script>
        @if (isset($tour->timeline) && !old('timeline'))
            timelineArray = @json(json_decode($tour->timeline));
        @endif
    </script> --}}

    {{-- <script>
        let timelineArray = [];

        // Populate the timelineArray from old data in the hidden input
        @if (old('timeline'))
            timelineArray = @json(json_decode(old('timeline')));
        @endif

        // Display old timeline data
        function displayTimeline() {
            $('#timelineList').empty(); // Clear existing timeline items
            timelineArray.forEach((timeline, idx) => {
                let timelineHtml = `<div class="timeline-item card p-2">
                    <strong>${timeline.title || 'No Title'}</strong><br>
                    <em>${timeline.subheadline || 'No Subheadline'}</em><br>
                    <p><strong>From:</strong> ${timeline.from || 'No From Time'}</p>
                    <p><strong>To:</strong> ${timeline.to || 'No To Time'}</p>
                    <p><strong>Body:</strong> ${timeline.body || 'No Body'}</p>
                    <button type="button" class="removeTimeline btn btn-danger btn-sm mb-1 ml-auto" data-index="${idx}">Remove</button>
                </div>`;
                $('#timelineList').append(timelineHtml);
            });

            // Update hidden input with JSON
            $('#timelineData').val(JSON.stringify(timelineArray));
        }

        // Initial display of timeline entries if there are old values
        displayTimeline();

        // Add new timeline entry to array and show in real-time
        $('#addTimeline').on('click', function() {
            let from = $('#timelineFrom').val() || null;
            let to = $('#timelineTo').val() || null;
            let title = $('#timelineTitle').val().trim() || null; // Trim whitespace
            let subheadline = $('#timelineSubheadline').val() || null;
            let body = $('#timelineBody').val().trim() || null; // Trim whitespace

            // Validation for title and body
            if (!title || !body) {
                alert('Title and Body are required.');
                return; // Prevent adding if either field is empty
            }

            // Add the timeline entry only if title and body are provided
            let timeline = {
                from: from,
                to: to,
                title: title,
                subheadline: subheadline,
                body: body
            };
            timelineArray.push(timeline);

            // Clear input fields
            $('#timelineFrom').val('');
            $('#timelineTo').val('');
            $('#timelineTitle').val('');
            $('#timelineSubheadline').val('');
            $('#timelineBody').val('');

            // Rebuild the timeline list
            displayTimeline();
        });

        // Remove timeline entry from the array and UI
        $(document).on('click', '.removeTimeline', function() {
            let index = $(this).data('index');
            timelineArray.splice(index, 1); // Remove from array

            // Rebuild timeline list
            displayTimeline();
        });

        // Submit form (AJAX or regular submission)
        $('#timelineForm').on('submit', function(e) {
            // Ensure that title and body are not empty
            let title = $('#timelineTitle').val().trim();
            let body = $('#timelineBody').val().trim();

            if (!title || !body) {
                e.preventDefault(); // Prevent form submission
                alert('Title and Body are required.');
            }
        });
    </script>

    <script>
        @if (isset($tour->timeline) && !old('timeline'))
            timelineArray = @json(json_decode($tour->timeline));
        @endif
    </script> --}}



    <script>
        // Initialize timelineArray with data from the database or old input
        let timelineArray = [];

        // Check if old timeline data exists (for when the form is submitted and errors are found)
        @if (old('timeline'))
            timelineArray = @json(json_decode(old('timeline')));
        @elseif (isset($service->timeline))
            // If the timeline data is available from the database
            timelineArray = @json(json_decode($service->timeline));
        @endif

        // Function to display the timeline entries
        function displayTimeline() {
            $('#timelineList').empty(); // Clear existing timeline items
            timelineArray.forEach((timeline, idx) => {
                let timelineHtml = `<div class="timeline-item card p-2">
                    <strong>${timeline.title || 'NA'}</strong><br>
                    <em>${timeline.subheadline || 'NA'}</em><br>
                    <p><strong>From:</strong> ${timeline.from || 'NA'}</p>
                    <p><strong>To:</strong> ${timeline.to || 'NA'}</p>
                    <p><strong>Body:</strong> ${timeline.body || 'NA'}</p>
                    <button type="button" class="removeTimeline btn btn-danger btn-sm mb-3 ml-2" data-index="${idx}">Remove</button>
                    <button type="button" class="editTimeline btn btn-primary btn-sm mb-3 ml-2" data-index="${idx}">Edit</button>
                </div>`;
                $('#timelineList').append(timelineHtml);
            });

            // Update hidden input with JSON
            $('#timelineData').val(JSON.stringify(timelineArray));
        }

        // Initial display of timeline entries if there are old values or data from the database
        displayTimeline();

        // Add new timeline entry to array and show in real-time
        $('#addTimeline').on('click', function() {
            let from = $('#timelineFrom').val() || null;
            let to = $('#timelineTo').val() || null;
            let title = $('#timelineTitle').val().trim() || null; // Trim whitespace
            let subheadline = $('#timelineSubheadline').val() || null;
            let body = $('#timelineBody').val().trim() || null; // Trim whitespace

            // Validation for title and body
            if (!title || !body) {
                alert('Title and Body are required.');
                return; // Prevent adding if either field is empty
            }

            // Add the timeline entry only if title and body are provided
            let timeline = {
                from: from,
                to: to,
                title: title,
                subheadline: subheadline,
                body: body
            };
            timelineArray.push(timeline);

            // Clear input fields
            $('#timelineFrom').val('');
            $('#timelineTo').val('');
            $('#timelineTitle').val('');
            $('#timelineSubheadline').val('');
            $('#timelineBody').val('');

            // Rebuild the timeline list
            displayTimeline();
        });

        // Remove timeline entry from the array and UI
        $(document).on('click', '.removeTimeline', function() {
            let index = $(this).data('index');
            timelineArray.splice(index, 1); // Remove from array

            // Rebuild timeline list
            displayTimeline();
        });

        // Open modal to edit timeline entry
        $(document).on('click', '.editTimeline', function() {
            let index = $(this).data('index');
            let timeline = timelineArray[index];

            // Populate the modal with current values
            $('#editTimelineFrom').val(timeline.from || '');
            $('#editTimelineTo').val(timeline.to || '');
            $('#editTimelineTitle').val(timeline.title || '');
            $('#editTimelineSubheadline').val(timeline.subheadline || '');
            $('#editTimelineBody').val(timeline.body || '');

            // Store the index of the timeline entry being edited
            $('#updateTimeline').data('index', index);

            // Show the modal
            $('#editTimelineModal').modal('show');
        });

        // Update the timeline entry in the array after editing
        $('#updateTimeline').on('click', function() {
            let index = $(this).data('index');
            let from = $('#editTimelineFrom').val() || null;
            let to = $('#editTimelineTo').val() || null;
            let title = $('#editTimelineTitle').val().trim() || null;
            let subheadline = $('#editTimelineSubheadline').val() || null;
            let body = $('#editTimelineBody').val().trim() || null;

            // Validation for title and body
            if (!title || !body) {
                alert('Title and Body are required.');
                return; // Prevent updating if either field is empty
            }

            // Update the timeline entry in the array
            timelineArray[index] = {
                from: from,
                to: to,
                title: title,
                subheadline: subheadline,
                body: body
            };

            // Rebuild the timeline list
            displayTimeline();

            // Close the modal
            $('#editTimelineModal').modal('hide');
        });
    </script>



@stop
