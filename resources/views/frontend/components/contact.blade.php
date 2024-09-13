<div class="col-lg-6 wow fadeInLeft" data-wow-delay="0.1s">
    <div class="contact-item">
        <div class="pb-5">
            <h4 class="text-primary">Contact Us</h4>
            <h1 class="display-4 mb-4">Get In Touch With Us</h1>
            <p class="mb-0">Fill out the form below, and let’s start the journey towards enhancing your team with
                top-tier candidates. Our experts are here to assist you every step of the way.
            </p>
        </div>
        <div class="d-flex align-items-center mb-4">
            <div class="bg-primary btn-lg-square rounded-circle p-4"><i class="fa fa-home text-white"></i>
            </div>
            @if ($setting->address)
                <div class="ms-4">
                    <h4>Addresses</h4>
                    <p class="mb-0">{{ $setting->address }}</p>
                </div>
            @endif
        </div>
        <div class="d-flex align-items-center mb-4">
            <div class="bg-primary btn-lg-square rounded-circle p-2"><i class="fa fa-phone-alt text-white"></i></div>
            @if ($setting->phone)
                <div class="ms-4">
                    <h4>Mobile</h4>
                    <p class="mb-0">{{ $setting->phone }}</p>
                </div>
            @endif
        </div>
        <div class="d-flex align-items-center mb-4">
            <div class="bg-primary btn-lg-square rounded-circle p-2"><i class="fa fa-envelope-open text-white"></i>
            </div>
            @if ($setting->email)
                <div class="ms-4">
                    <h4>Email</h4>
                    <p class="mb-0">{{ $setting->email }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
<div class="col-lg-6 wow fadeInRight" data-wow-delay="0.3s">
    <form action="{{ route('contact.send') }}" method="POST">
        @csrf
        <div class="row g-3">
            <div class="col-lg-12 col-xl-6">
                <div class="form-floating">
                    <input type="text" class="form-control" name="name" placeholder="Your Name">
                    @error('name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    <label for="name">Your Name</label>
                </div>
            </div>
            <div class="col-lg-12 col-xl-6">
                <div class="form-floating">
                    <input type="email" class="form-control" name="email" placeholder="Your Email">
                    @error('email')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    <label for="email">Your Email</label>
                </div>
            </div>
            <div class="col-lg-12 col-xl-12">
                <div class="form-floating">
                    <input type="phone" class="form-control" name="phone" placeholder="Phone">
                    @error('phone')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    <label for="phone">Your Phone</label>
                </div>
            </div>

            <div class="col-12">
                <div class="form-floating">
                    <textarea class="form-control" placeholder="Leave a message here" name="message" style="height: 160px"></textarea>
                    @error('message')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    <label for="message">Message</label>
                </div>
            </div>
            <div class="col-12">
                <button class="btn btn-primary w-100 py-3">Send Message</button>
            </div>
        </div>
    </form>
</div>
