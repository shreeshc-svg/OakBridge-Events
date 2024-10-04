<div class="container-fluid faq py-5">
    <div class="container py-5">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6 wow fadeInLeft" data-wow-delay="0.1s">
                <div class="pb-5">
                    <h4 class="text-primary">FAQs</h4>
                    <h1 class="display-4">Get the Answers to Common Questions</h1>
                </div>
                <div class="accordion bg-light rounded p-4" id="accordionExample">
                    @foreach ($faqs as $faq)
                        <div class="accordion-item border-0 mb-4">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button text-dark fs-5 fw-bold rounded-top" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapse{{ $faq->id }}"
                                    aria-expanded="true" aria-controls="collapseTOne">
                                    {{ $faq->title }}
                                </button>
                            </h2>
                            <div id="collapse{{ $faq->id }}"
                                class="accordion-collapse collapse @if ($loop->first) show @endif"
                                aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                <div class="accordion-body my-2">

                                    <p>{!! $faq->body !!}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
            <div class="col-lg-6 wow fadeInRight" data-wow-delay="0.3s">
                <div class="faq-img RotateMoveRight rounded">
                    <img src="{{ asset('public/assets/img/faq-img.jpg') }}" class="img-fluid rounded w-100"
                        alt="Image">
                    {{-- <a class="faq-btn btn btn-primary rounded-pill text-white py-3 px-5" href="#">Read More
                        Q & A <i class="fas fa-arrow-right ms-2"></i></a> --}}
                </div>
            </div>
        </div>
    </div>
</div>
