<nav class="navbar navbar-expand-lg navbar-dark px-5 py-3 py-lg-0">
    <a href="index.html" class="navbar-brand p-0">
        <h1 class="m-0"><i class="fa fa-user-tie me-2"></i>Gem Consultant</h1>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="fa fa-bars"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto py-0">
            <a href="{{ route('home') }}"
                class="nav-item nav-link @if (Route::is('home')) active @endif">Home</a>
            <a href="{{ route('about') }}"
                class="nav-item nav-link @if (Route::is('about')) active @endif">About</a>

            @foreach ($service_categories as $category)
                @if ($category->services_count)
                    <div class="nav-item dropdown ">
                        <a href="#"
                            class="nav-link dropdown-toggle @if (Route::is('service') || Route::is('service.detail')) active @endif "
                            data-bs-toggle="dropdown">{{ $category->title }}</a>
                        <div class="dropdown-menu m-0">
                            @foreach ($category->services as $service)
                                @if ($service->featured == 1)
                                    <a href="{{ route('service.detail', $service->slug) }}"
                                        class="dropdown-item">{{ $service->title }}</a>
                                @endif
                            @endforeach
                            <a href="{{ route('service') }}" class="dropdown-item">View All</a>

                        </div>
                    </div>
                @endif
            @endforeach

            <a href="{{ route('testimonial') }}" class="nav-item nav-link">Testimonials</a>
            <a href="{{ route('pricing') }}" class="nav-item nav-link">Pricing</a>
            <a href="{{ route('blog') }}" class="nav-item nav-link">Blogs</a>
            <a href="{{ route('contact') }}" class="nav-item nav-link">Contact</a>
        </div>
        {{-- <butaton type="button" class="btn text-primary ms-3" data-bs-toggle="modal" data-bs-target="#searchModal">
            <i class="fa fa-search"></i>
        </butaton> --}}
        <a href="tel:{{ str_replace(' ', '', $setting->phone) }}" class="btn btn-primary py-2 px-4 ms-3">🤙
            {{ $setting->phone }}</a>
    </div>
</nav>
