@extends('frontend.layouts.app')
@section('title', 'Benefits' . ' ' . '-' . '' . $setting->site_title)
@section('keywords', $setting->site_keywords)
@section('description', $setting->site_description)
@section('content')

    <!--====== Start Breadcrumb Section ======-->
    <section class="page-banner bg_cover p-r z-1 text-white"
        style="background-image: url(public/assets/images/bg/breadcrumbs-bg.jpg);">
        <div class="container">
            <h1 class="page-title">Benifits</h1>
            <div class="page-breadcrumb">
                <ul class="breadcrumb-link">
                    <li><a href="index-2.html">Home</a></li>
                    <li class="active">Benefits</li>
                </ul>
            </div>
        </div>
    </section>
    <!--====== End Breadcrumb Section ======-->

    <!--====== Start Strategy Section ======-->
    <section class="strategy-section pt-130 pb-75">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-6 col-lg-12">
                    <div class="section-title text-center mb-55 wow fadeInDown">
                        <span class="sub-title"><span class="line line1"></span>Garage Vaala<span
                                class="line line2"></span></span>
                        <h2>Our Benefits</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="strategy-content-box mb-50 wow fadeInRight">
                        <div class="tab-content-box">
                            <ul class="nav nav-tabs mb-50 justify-content-center">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#pickupdrop">Pick Up &
                                        Drop</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#nightservice">Over Night
                                        Services</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#autoupdate">Auto Updates</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#minimalcalling">Minimal
                                        Calling</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#robustparts">Robust Parts</a>
                                </li>
                            </ul>
                            <div class="tab-content mb-30">
                                <div class="tab-pane fade show active" id="pickupdrop">
                                    <div class="section-title text-center mb-55">
                                        <h2>Pick Up & Drop</h2>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-2"></div>
                                        <div class="col-lg-8 text-center">
                                            <p>At Garage Vaala, we understand that your time is precious, and
                                                convenience matters. That's why we're thrilled to offer our valued
                                                customers the added convenience of our Pick-Up and Drop service. We've
                                                designed this service with you in mind, ensuring that your automobile
                                                care experience is as hassle-free as possible.</p>
                                        </div>
                                        <div class="col-lg-2"></div>
                                    </div>
                                    <!--====== Start Process Section ======-->
                                    <section class="how-work-section pt-70 pb-70">
                                        <div class="container">
                                            <div class="row justify-content-center">
                                                <div class="col-xl-12 col-lg-12">
                                                    <div class="section-title text-center mb-65 wow fadeInDown">
                                                        <h2>How It Works</h2>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row justify-content-center g-0">
                                                <div class="col-lg-12 col-md-12 process-item">
                                                    <div class="row process-inner-item mb-40 wow fadeInUp">
                                                        <div class="col-md-3">
                                                            <div class="icon">
                                                                <i class="flaticon-maintenance"></i>
                                                                <span class="number">01</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <div class="text">
                                                                <h4 class="title">Schedule Your Service</h4>
                                                                <p>When you're due for maintenance, repairs, or any of
                                                                    our services, simply give us a call at
                                                                    +91-8851200129 or book an appointment through our
                                                                    online platform. Let us know that you're interested
                                                                    in the Pick-Up and Drop service.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-12 process-item">
                                                    <div class="row process-inner-item mb-40 wow fadeInUp">
                                                        <div class="col-md-3">
                                                            <div class="icon">
                                                                <i class="flaticon-maintenance"></i>
                                                                <span class="number">02</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <div class="text">
                                                                <h4 class="title">Confirmation</h4>
                                                                <p>Our friendly team will confirm your appointment
                                                                    details, including the preferred date and time for
                                                                    pick-up</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-12 process-item">
                                                    <div class="row process-inner-item mb-40 wow fadeInUp">
                                                        <div class="col-md-3">
                                                            <div class="icon">
                                                                <i class="flaticon-maintenance"></i>
                                                                <span class="number">03</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <div class="text">
                                                                <h4 class="title">Pick-Up Arrangements</h4>
                                                                <p>On the scheduled day, one of our courteous drivers
                                                                    will arrive at your location to collect your
                                                                    vehicle. We'll handle all the necessary paperwork,
                                                                    ensuring a smooth transition.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-12 process-item">
                                                    <div class="row process-inner-item mb-40 wow fadeInUp">
                                                        <div class="col-md-3">
                                                            <div class="icon">
                                                                <i class="flaticon-maintenance"></i>
                                                                <span class="number">04</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <div class="text">
                                                                <h4 class="title">Service Excellence</h4>
                                                                <p> Our skilled technicians will perform the required
                                                                    services with the utmost care and precision. Rest
                                                                    assured, your vehicle is in expert hands.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-12 process-item">
                                                    <div class="row process-inner-item mb-40 wow fadeInUp">
                                                        <div class="col-md-3">
                                                            <div class="icon">
                                                                <i class="flaticon-maintenance"></i>
                                                                <span class="number">05</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <div class="text">
                                                                <h4 class="title">Quality Checks</h4>
                                                                <p>Before we return your vehicle, we conduct thorough
                                                                    quality checks to ensure that all services have been
                                                                    completed to our high standards</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-12 process-item">
                                                    <div class="row process-inner-item mb-40 wow fadeInUp">
                                                        <div class="col-md-3">
                                                            <div class="icon">
                                                                <i class="flaticon-maintenance"></i>
                                                                <span class="number">06</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <div class="text">
                                                                <h4 class="title">Drop-off</h4>
                                                                <p> Once your vehicle is ready, we'll arrange a
                                                                    convenient time to deliver it back to you. Your
                                                                    vehicle will be returned in pristine condition.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                    <!--====== End Process Section ======-->
                                </div>
                                <div class="tab-pane fade" id="nightservice">
                                    <div class="section-title text-center mb-55">
                                        <h2>Night Service</h2>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-2"></div>
                                        <div class="col-lg-8 text-center">
                                            <p>We know that your busy schedule might not always align with regular
                                                daytime hours. That's why we're excited to introduce our Night Service,
                                                designed to cater to your automotive needs even outside of conventional
                                                working hours. With our Night Service, we're here to serve you around
                                                the clock, ensuring your vehicle gets the attention it deserves,
                                                whenever it's most convenient for you.</p>
                                        </div>
                                        <div class="col-lg-2"></div>
                                    </div>
                                    <!--====== Start Process Section ======-->
                                    <section class="how-work-section pt-70 pb-70">
                                        <div class="container">
                                            <div class="row justify-content-center">
                                                <div class="col-xl-12 col-lg-12">
                                                    <div class="section-title text-center mb-65 wow fadeInDown">
                                                        <h2>Why Choose Night Service</h2>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row justify-content-center g-0">
                                                <div class="col-lg-12 col-md-12 process-item">
                                                    <div class="row process-inner-item mb-40 wow fadeInUp">
                                                        <div class="col-md-3">
                                                            <div class="icon">
                                                                <i class="flaticon-maintenance"></i>
                                                                <span class="number">01</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <div class="text">
                                                                <h4 class="title">Convenience</h4>
                                                                <p>Our Night Service is all about convenience for you.
                                                                    We understand that your time is valuable, and we're
                                                                    here to make taking care of your vehicle as
                                                                    hassle-free as possible.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-12 process-item">
                                                    <div class="row process-inner-item mb-40 wow fadeInUp">
                                                        <div class="col-md-3">
                                                            <div class="icon">
                                                                <i class="flaticon-maintenance"></i>
                                                                <span class="number">02</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <div class="text">
                                                                <h4 class="title">Quality</h4>
                                                                <p>Just like our daytime service, our Night Service
                                                                    upholds the same high standards of quality and
                                                                    expertise. You can trust us to provide top-notch
                                                                    service, no matter the hour.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-12 process-item">
                                                    <div class="row process-inner-item mb-40 wow fadeInUp">
                                                        <div class="col-md-3">
                                                            <div class="icon">
                                                                <i class="flaticon-maintenance"></i>
                                                                <span class="number">03</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <div class="text">
                                                                <h4 class="title">Peace of Mind</h4>
                                                                <p> Whether you're dealing with a sudden issue or you
                                                                    simply prefer nighttime appointments, our Night
                                                                    Service gives you the peace of mind that your
                                                                    vehicle is in capable hands.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                    <!--====== End Process Section ======-->
                                </div>
                                <div class="tab-pane fade" id="autoupdate">
                                    <div class="section-title text-center mb-55">
                                        <h2>Auto Update</h2>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-2"></div>
                                        <div class="col-lg-8 text-center">
                                            <p>We're dedicated to providing you with a seamless and transparent
                                                automotive service experience. That's why we're excited to introduce our
                                                "Auto Updates" feature, designed to keep you informed every step of the
                                                way. With Auto Updates, you'll have real-time access to the status of
                                                your vehicle, ensuring you're always in the loop about its condition and
                                                the progress of our services.</p>
                                        </div>
                                        <div class="col-lg-2"></div>
                                    </div>
                                    <!--====== Start Process Section ======-->
                                    <section class="how-work-section pt-70 pb-70">
                                        <div class="container">
                                            <div class="row justify-content-center">
                                                <div class="col-xl-12 col-lg-12">
                                                    <div class="section-title text-center mb-65 wow fadeInDown">
                                                        <h2>How Auto Updates Works</h2>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row justify-content-center g-0">
                                                <div class="col-lg-12 col-md-12 process-item">
                                                    <div class="row process-inner-item mb-40 wow fadeInUp">
                                                        <div class="col-md-3">
                                                            <div class="icon">
                                                                <i class="flaticon-maintenance"></i>
                                                                <span class="number">01</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <div class="text">
                                                                <h4 class="title">Real-Time Notifications</h4>
                                                                <p>Once you entrust your vehicle to us, you'll receive
                                                                    real-time notifications via your preferred
                                                                    communication method—text, email, or both</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-12 process-item">
                                                    <div class="row process-inner-item mb-40 wow fadeInUp">
                                                        <div class="col-md-3">
                                                            <div class="icon">
                                                                <i class="flaticon-maintenance"></i>
                                                                <span class="number">02</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <div class="text">
                                                                <h4 class="title">Service Milestones</h4>
                                                                <p>You'll be informed when your vehicle reaches
                                                                    significant service milestones, such as when the
                                                                    diagnostic phase is complete, repairs have started,
                                                                    or your vehicle is ready for pickup</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-12 process-item">
                                                    <div class="row process-inner-item mb-40 wow fadeInUp">
                                                        <div class="col-md-3">
                                                            <div class="icon">
                                                                <i class="flaticon-maintenance"></i>
                                                                <span class="number">03</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <div class="text">
                                                                <h4 class="title">Detailed Updates</h4>
                                                                <p>Our Auto Updates will provide you with detailed
                                                                    information about the work being done on your
                                                                    vehicle, including descriptions of services,
                                                                    repairs, and any additional recommendations from our
                                                                    technicians.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-12 process-item">
                                                    <div class="row process-inner-item mb-40 wow fadeInUp">
                                                        <div class="col-md-3">
                                                            <div class="icon">
                                                                <i class="flaticon-maintenance"></i>
                                                                <span class="number">04</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <div class="text">
                                                                <h4 class="title">Estimated Completion Time</h4>
                                                                <p> We'll give you an estimated time for when your
                                                                    vehicle will be ready for pickup, allowing you to
                                                                    plan your schedule accordingly.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                    <!--====== End Process Section ======-->
                                </div>
                                <div class="tab-pane fade" id="minimalcalling">
                                    <div class="section-title text-center mb-55">
                                        <h2>Minimal Calling</h2>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-2"></div>
                                        <div class="col-lg-8 text-center">
                                            <p>At Garage Vaala, we value your time and understand that modern
                                                communication should be effortless and efficient. That's why we're
                                                excited to introduce our "Minimal Calling" approach, designed to provide
                                                you with a streamlined and hassle-free experience for all your
                                                automotive service needs.</p>
                                        </div>
                                        <div class="col-lg-2"></div>
                                    </div>
                                    <!--====== Start Process Section ======-->
                                    <section class="how-work-section pt-70 pb-70">
                                        <div class="container">
                                            <div class="row justify-content-center">
                                                <div class="col-xl-12 col-lg-12">
                                                    <div class="section-title text-center mb-65 wow fadeInDown">
                                                        <h2>How Our Minimal Calling Approach Works</h2>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row justify-content-center g-0">
                                                <div class="col-lg-12 col-md-12 process-item">
                                                    <div class="row process-inner-item mb-40 wow fadeInUp">
                                                        <div class="col-md-3">
                                                            <div class="icon">
                                                                <i class="flaticon-maintenance"></i>
                                                                <span class="number">01</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <div class="text">
                                                                <h4 class="title">Digital Booking</h4>
                                                                <p> Instead of spending time on the phone, you can
                                                                    easily schedule your appointments through our
                                                                    user-friendly online platform. Visit our website and
                                                                    choose the services you need, select your preferred
                                                                    date and time, and you're all set.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-12 process-item">
                                                    <div class="row process-inner-item mb-40 wow fadeInUp">
                                                        <div class="col-md-3">
                                                            <div class="icon">
                                                                <i class="flaticon-maintenance"></i>
                                                                <span class="number">02</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <div class="text">
                                                                <h4 class="title">Auto Updates</h4>
                                                                <p>Our state-of-the-art Auto Updates feature keeps you
                                                                    informed about the status of your vehicle in
                                                                    real-time via text or email. You'll receive
                                                                    notifications at every step of the service process,
                                                                    from diagnostics to completion.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-12 process-item">
                                                    <div class="row process-inner-item mb-40 wow fadeInUp">
                                                        <div class="col-md-3">
                                                            <div class="icon">
                                                                <i class="flaticon-maintenance"></i>
                                                                <span class="number">03</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <div class="text">
                                                                <h4 class="title">Digital Payment</h4>
                                                                <p>Say goodbye to phone calls for payment processing.
                                                                    Our secure online payment system allows you to
                                                                    settle your bill conveniently through our website.
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-12 process-item">
                                                    <div class="row process-inner-item mb-40 wow fadeInUp">
                                                        <div class="col-md-3">
                                                            <div class="icon">
                                                                <i class="flaticon-maintenance"></i>
                                                                <span class="number">04</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <div class="text">
                                                                <h4 class="title">Service Documentation</h4>
                                                                <p>After the service is complete, we'll provide you with
                                                                    detailed digital documentation outlining the work
                                                                    done, recommendations, and any additional
                                                                    information you might need.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                    <!--====== End Process Section ======-->
                                </div>
                                <div class="tab-pane fade" id="robustparts">
                                    <div class="section-title text-center mb-55">
                                        <h2>Robust Parts</h2>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-2"></div>
                                        <div class="col-lg-8 text-center">
                                            <p>We understand that the durability and reliability of automotive parts are
                                                crucial for the performance of your vehicle. That's why we're proud to
                                                introduce our "Robust Parts" initiative – a commitment to using only the
                                                most durable and high-quality components in all our services and repairs
                                            </p>
                                        </div>
                                        <div class="col-lg-2"></div>
                                    </div>
                                    <!--====== Start Process Section ======-->
                                    <section class="how-work-section pt-70 pb-70">
                                        <div class="container">
                                            <div class="row justify-content-center">
                                                <div class="col-xl-12 col-lg-12">
                                                    <div class="section-title text-center mb-65 wow fadeInDown">
                                                        <h2>Why Robust Parts Matter</h2>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row justify-content-center g-0">
                                                <div class="col-lg-12 col-md-12 process-item">
                                                    <div class="row process-inner-item mb-40 wow fadeInUp">
                                                        <div class="col-md-3">
                                                            <div class="icon">
                                                                <i class="flaticon-maintenance"></i>
                                                                <span class="number">01</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <div class="text">
                                                                <h4 class="title">Longevity</h4>
                                                                <p>Vehicles are investments, and we believe in ensuring
                                                                    that the parts we install contribute to the
                                                                    long-term health and longevity of your vehicle</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-12 process-item">
                                                    <div class="row process-inner-item mb-40 wow fadeInUp">
                                                        <div class="col-md-3">
                                                            <div class="icon">
                                                                <i class="flaticon-maintenance"></i>
                                                                <span class="number">02</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <div class="text">
                                                                <h4 class="title">Performance</h4>
                                                                <p>Robust parts directly impact the performance of your
                                                                    vehicle. Whether it's smoother handling, improved
                                                                    fuel efficiency, or optimal engine performance, our
                                                                    parts deliver results.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-12 process-item">
                                                    <div class="row process-inner-item mb-40 wow fadeInUp">
                                                        <div class="col-md-3">
                                                            <div class="icon">
                                                                <i class="flaticon-maintenance"></i>
                                                                <span class="number">03</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <div class="text">
                                                                <h4 class="title">Safety</h4>
                                                                <p>Your safety on the road is paramount. Our robust
                                                                    parts undergo stringent quality checks to ensure
                                                                    they meet the highest safety standards.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                    <!--====== End Process Section ======-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section><!--====== End Strategy Section ======-->


    <!--====== End Testimonial Section ======-->

@stop
