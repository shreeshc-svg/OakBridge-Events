@extends('frontend.layouts.app')
@section('title', 'OakBridge Events Gallery | Highlights from the India Law, AI & Tech Summit 2025')
@section('keywords', 'legal tech, law ai, tech summit, legal tech summit, new technology, technology summit, legal tech leaders, india law ai, ai in law, ai legal tech photos, summit legal tech images')
@section('description', 'The India Law AI Tech Summit 2025 envisions Indias premier annual forum for legal innovation. A dynamic experience designed for maximum engagement and unparalled access celebrating Law AI Tech pioneers leaders and innovators driving Law AI Tech revolution.')
@section('content')
    <style>
        .gallery-image {
            cursor: pointer;
            /* Show pointer cursor on hover */
        }
    </style>


    <!--Page Title-->
    {{-- <section class="page-title"
         style="background-image:linear-gradient(rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.75)), url({{ asset('public/assets/images/background/bread2.webp') }}); background-size: cover; background-position: center;"> --}}
         <section class="page-title"
         style="background-image: url({{ asset('public/assets/images/background/bread2.webp') }}); background-size: cover; background-position: center;">
        {{-- <section class="page-title" style="background-image:url({{ asset('public/assets/images/background/5.jpg') }});"> --}}
        <div class="auto-container">
            <h1>Image Gallery</h1>
            <ul class="bread-crumb clearfix">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li>Image Gallery</li>
            </ul>
        </div>
    </section>
    <!--End Page Title-->
    <section class="py-5">
        <div class="container">
            <div class="row">
                @foreach ($images as $key => $image)
                    <div class="col-md-4 mb-4">
                        <img src="{{ asset('public/uploads/images/our-gallery/' . $image->name) }}" alt="Gallery Image"
                            class="img-fluid gallery-image" data-index="{{ $key }}"
                            data-src="{{ asset('public/uploads/images/our-gallery/' . $image->name) }}">
                    </div>
                @endforeach
            </div>
        </div>
    </section>

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
