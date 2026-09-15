<div class="col-md-12 col-lg-4 order-md-1 order-2 sticky-sidebar ">
    <div class="service-sidebar">
        <div class="service-sidebar__single">
            <h3 class="service-sidebar__title">Categories</h3><!-- /.service-sidebar__title -->
            <ul class="list-unstyled service-sidebar__nav">
                @foreach ($categories as $category)
                    <li><a style="font-weight: 500; font-size:16px !important;"
                            href="{{ route('service', ['category' => $category->slug]) }}">{{ $category->title }}
                            ({{ $category->services->count() }})
                        </a>
                    </li>
                @endforeach

            </ul><!-- /.list-unstyled service-sidebar__nav -->
        </div><!-- /.service-sidebar__single -->
        <div class="service-sidebar__single">
            <div class="">

                <h3>Request FREE Callback</h3>

                <form action="{{ route('contact.send') }}" method="post">
                    @csrf

                    <div class="mb-2">
                        <label for="">Name</label><input type="text" name="name"
                            class="form-control shadow-none" placeholder="Your Name" value="{{ old('name') }}"
                            id="" required>
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                    </div>
                    <div class="mb-2">
                        <label for="">Email</label><input type="email" name="email"
                            class="form-control shadow-none" placeholder="Your Email" value="{{ old('email') }}"
                            id="" required>
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-2">
                        <label for="">Phone</label><input type="tel" name="phone"
                            class="form-control shadow-none" placeholder="Your Phone" value="{{ old('phone') }}"
                            id="" required>
                        @error('phone')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-2">
                        <label for="">Message</label>
                        <textarea name="message" id="" class="form-control shadow-none" cols="30" rows="4"
                            value="{{ old('message') }}"></textarea>
                        @error('message')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="">
                        <button type="submit" class="alefox-btn">
                            <span class="alefox-btn__item"></span>
                            <span class="alefox-btn__item"></span>
                            <span class="alefox-btn__item"></span>
                            <span class="alefox-btn__item"></span>
                            Send Request
                        </button>
                    </div>
                </form>
            </div><!-- /.service-sidebar__contact -->
        </div>
    </div>
</div>
