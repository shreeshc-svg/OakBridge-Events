@extends('frontend.layouts.app')
@section('title', 'Vidhi Samman' . ' ' . '-' . ' ' . $setting->site_title)
@section('keywords', $setting->site_keywords)
@section('description', $setting->site_description)
@section('content')
    <style>
        .gallery-image {
            cursor: pointer;
            /* Show pointer cursor on hover */
        }
    </style>


    <!--Page Title-->
    <section class="page-title"
        style="background-image:linear-gradient(rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.75)), url({{ asset('public/assets/images/background/bread.webp') }}); background-size: cover; background-position: center;">
        {{-- <section class="page-title" style="background-image:url({{ asset('public/assets/images/background/5.jpg') }});"> --}}
        <div class="auto-container">
            <h1>Vidhi Samman</h1>
            <ul class="bread-crumb clearfix">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li>Vidhi Samman </li>
            </ul>
        </div>
    </section>
    <!--End Page Title-->

    {{-- sarvoch 2025 --}}

    @if ($sarvoch2025->count())
        <section class="py-5">
            <div class="container">
                <div class="row py-2 text-center">
                    <div class="sec-title pb-0 mb-4">
                        <h2>Sarvoch Vidhi Samman 2025</h2>
                        <p class="title mb-0 text-center pb-0 mb-0">Outstanding Contributions to the Legal Field</p>
                    </div>
                </div>
                <div class="row align-items-center g-4">
                    <div class="col-md-4">
                        <img class="img-fluid rounded" src="{{ asset('public/assets/images/certificate/sarvoch.webp') }}"
                            alt="">
                    </div>
                    @foreach ($sarvoch2024 as $key => $image)
                        <div class="col-md-4 mb-4">
                            <img src="{{ asset('public/uploads/images/vidhi/' . $image->image) }}" alt="Gallery Image"
                                class="img-fluid gallery-image rounded" data-index="{{ $key }}"
                                data-src="{{ asset('public/uploads/images/vidhi/' . $image->image) }}">
                            <h4 class="text-dark text-center">{{ $image->title }}</h4>
                            <p class="text-center">{{ $image->body }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- vishist 2025 --}}

    @if ($vishist2025->count())
        <section class="py-5">
            <div class="container">
                <div class="row py-2 text-center">
                    <div class="sec-title pb-0 mb-4">
                        <h2> Vishisht Vidhi Samman 2025</h2>
                        <p class="title mb-0 text-center pb-0 mb-0">Significant Contributions to the Legal Field</p>
                    </div>
                </div>
                <div class="row align-items-center g-4">
                    <div class="col-md-4">
                        <img class="img-fluid rounded" src="{{ asset('public/assets/images/certificate/vishist.webp') }}"
                            alt="">
                    </div>
                    @foreach ($vishist2024 as $key => $image)
                        <div class="col-md-4 mb-4">
                            <img src="{{ asset('public/uploads/images/vidhi/' . $image->image) }}" alt="Gallery Image"
                                class="img-fluid gallery-image rounded" data-index="{{ $key }}"
                                data-src="{{ asset('public/uploads/images/vidhi/' . $image->image) }}">
                            <h4 class="text-dark text-center">{{ $image->title }}</h4>
                            <p class="text-center">{{ $image->body }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- vidhi samman 2025 --}}
    @if ($vidhi2025->count())
        <section class="py-5">
            <div class="container">
                <div class="row py-2 text-center">
                    <div class="sec-title pb-0 mb-4">
                        <h2>VIDHI SAMMAN 2024</h2>
                        <p class="title mb-0 text-center pb-0 mb-0">Significant Contributions to the Legal Field</p>
                    </div>
                </div>
                <div class="row align-items-center g-4">
                    <div class="col-md-4">
                        <img class="img-fluid rounded" src="{{ asset('public/assets/images/certificate/vidhi.webp') }}"
                            alt="">
                    </div>
                    @foreach ($vidhi2024 as $key => $image)
                        <div class="col-md-4 mb-4">
                            <img src="{{ asset('public/uploads/images/vidhi/' . $image->image) }}" alt="Gallery Image"
                                class="img-fluid gallery-image rounded" data-index="{{ $key }}"
                                data-src="{{ asset('public/uploads/images/vidhi/' . $image->image) }}">
                            <h4 class="text-dark text-center">{{ $image->title }}</h4>
                            <p class="text-center">{{ $image->body }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

    @endif



    {{-- sarvoch 2024 --}}
    @if ($sarvoch2024->count())
        <section class="py-5">
            <div class="container">
                <div class="row py-2 text-center">
                    <div class="sec-title pb-0 mb-4">
                        <h2>Sarvoch Vidhi Samman 2024</h2>
                        <p class="title mb-0 text-center pb-0 mb-0">Outstanding Contributions to the Legal Field</p>
                    </div>
                </div>
                <div class="row align-items-center g-4">
                    <div class="col-md-4">
                        <img class="img-fluid rounded" src="{{ asset('public/assets/images/certificate/sarvoch.webp') }}"
                            alt="">
                    </div>
                    @foreach ($sarvoch2024 as $key => $image)
                        <div class="col-md-4 mb-4">
                            <img src="{{ asset('public/uploads/images/vidhi/' . $image->image) }}" alt="Gallery Image"
                                class="img-fluid gallery-image rounded" data-index="{{ $key }}"
                                data-src="{{ asset('public/uploads/images/vidhi/' . $image->image) }}">
                            <h4 class="text-dark text-center">{{ $image->title }}</h4>
                            <p class="text-center">{{ $image->body }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- vishist 2024 --}}
    @if ($vishist2024->count())
        <section class="py-5">
            <div class="container">
                <div class="row py-2 text-center">
                    <div class="sec-title pb-0 mb-4">
                        <h2> Vidhi Vishist Samman 2024</h2>
                        <p class="title mb-0 text-center pb-0 mb-0">Significant Contributions to the Legal Field</p>
                    </div>
                </div>
                <div class="row align-items-center g-4">
                    <div class="col-md-4">
                        <img class="img-fluid rounded" src="{{ asset('public/assets/images/certificate/vishist.webp') }}"
                            alt="">
                    </div>
                    @foreach ($vishist2024 as $key => $image)
                        <div class="col-md-4 mb-4">
                            <img src="{{ asset('public/uploads/images/vidhi/' . $image->image) }}" alt="Gallery Image"
                                class="img-fluid gallery-image rounded" data-index="{{ $key }}"
                                data-src="{{ asset('public/uploads/images/vidhi/' . $image->image) }}">
                            <h4 class="text-dark text-center">{{ $image->title }}</h4>
                            <p class="text-center">{{ $image->body }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- vidhi samman 2024 --}}
    @if ($vidhi2024->count())
        <section class="py-5">
            <div class="container">
                <div class="row py-2 text-center">
                    <div class="sec-title pb-0 mb-4">
                        <h2>VIDHI SAMMAN 2024</h2>
                        <p class="title mb-0 text-center pb-0 mb-0">Significant Contributions to the Legal Field</p>
                    </div>
                </div>
                <div class="row align-items-center g-4">
                    <div class="col-md-4">
                        <img class="img-fluid rounded" src="{{ asset('public/assets/images/certificate/vidhi.webp') }}"
                            alt="">
                    </div>
                    @foreach ($vidhi2024 as $key => $image)
                        <div class="col-md-4 mb-4">
                            <img src="{{ asset('public/uploads/images/vidhi/' . $image->image) }}" alt="Gallery Image"
                                class="img-fluid gallery-image rounded" data-index="{{ $key }}"
                                data-src="{{ asset('public/uploads/images/vidhi/' . $image->image) }}">
                            <h4 class="text-dark text-center">{{ $image->title }}</h4>
                            <p class="text-center">{{ $image->body }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

    @endif




    <!-- Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Image Preview</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" class="img-fluid" alt="Modal Image">
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" id="prevImage">Previous</button>
                    <button type="button" class="btn btn-secondary" id="nextImage">Next</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const images = document.querySelectorAll(".gallery-image");
            const modal = document.getElementById("imageModal");
            const modalImage = document.getElementById("modalImage");
            const nextButton = document.getElementById("nextImage");
            const prevButton = document.getElementById("prevImage");

            let currentIndex = 0;

            images.forEach((image, index) => {
                image.addEventListener("click", function() {
                    currentIndex = index;
                    showImage();
                    $(modal).modal("show");
                });
            });

            nextButton.addEventListener("click", function() {
                currentIndex = (currentIndex + 1) % images.length;
                showImage();
            });

            prevButton.addEventListener("click", function() {
                currentIndex = (currentIndex - 1 + images.length) % images.length;
                showImage();
            });

            function showImage() {
                const currentImage = images[currentIndex];
                modalImage.src = currentImage.dataset.src;
            }
        });
    </script>

@stop

@section('js')

@stop
