<div class="container-fluid testimonial bg-light py-5">
    <div class="container py-5">
        <div class="row g-4 align-items-center">
            <div class="col-xl-4 wow fadeInLeft" data-wow-delay="0.1s">
                <div class="h-100 rounded">
                    <h4 class="text-primary">Our Feedbacks </h4>
                    <h1 class="display-5 mb-4">Clients are Talking</h1>
                    <p class="mb-4">This headline encourages readers to explore genuine client feedback, emphasizing
                        the positive experiences and satisfaction of those who've partnered with SKY LEAD TRAVELS PVT.
                        LTD. It builds trust by showcasing success stories and client endorsements.</p>
                    {{-- <a class="btn btn-primary rounded-pill text-white py-3 px-5" href="#">Read All Reviews
                        <i class="fas fa-arrow-right ms-2"></i></a> --}}
                </div>
            </div>
            <div class="col-xl-8">
                <div class="testimonial-carousel owl-carousel wow fadeInUp" data-wow-delay="0.1s">
                    @foreach ($testimonials as $testimonial)
                        <div class="testimonial-item bg-white rounded p-4 wow fadeInUp" data-wow-delay="0.3s">
                            <div class="d-flex">
                                <div><i class="fas fa-quote-left fa-3x text-dark me-3"></i></div>
                                <p class="mt-4">{{ $testimonial->body }}
                                </p>
                            </div>
                            <div class="d-flex justify-content-end">
                                <div class="my-auto text-end">
                                    <h5>{{ $testimonial->title }}</h5>
                                    <p class="mb-0">{{ $testimonial->location }}</p>
                                </div>
                                <div class="bg-white rounded-circle ms-3">
                                    <img src="{{ asset('public/uploads/images/testimonial/' . $testimonial->image) }}"
                                        class="rounded-circle p-2"
                                        style="width: 80px; height: 80px; border: 1px solid; border-color: var(--bs-primary);"
                                        alt="">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
