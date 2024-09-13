<div class="rv-blog-details-right rv-blog-details-search">
    <h3 class="rv-blog-details-right__title">Search</h3>
    <form action="{{ route('blog') }}">
        <input type="text" @if (Route::is('blog')) value="{{ $search }}" @endif name="search"
            id="rv-blog-details-search-input" placeholder="Search Here...">
        <button type="submit"><i class="fa-regular fa-magnifying-glass"></i></button>
    </form>
</div>

<div class="rv-blog-details-right rv-blog-details-categories">
    <h3 class="rv-blog-details-right__title">Categories</h3>
    <ul>
        @foreach ($categories as $category)
            <li>
                <a href="{{ route('blog', ['category' => $category->slug]) }}" class="rv-blog-details-category">
                    <span class="rv-blog-details-category-name">{{ $category->title }}</span>
                </a>
            </li>
        @endforeach


    </ul>
</div>

<div class="rv-blog-details-right rv-blog-details-recents">
    <h3 class="rv-blog-details-right__title">Recent Posts</h3>
    @foreach ($recently as $post)
        <div class="rv-recent-blog">
            @if ($post->image)
                <img class="rv-recent-blog__img" src="{{ asset('public/uploads/images/post/' . $post->image) }}"
                    alt="blog image">
            @else
                <img class="rv-recent-blog__img" src="{{ asset('public/uploads/images/no-image.jpg') }}"
                    alt="blog image">
            @endif
            <div class="rv-recent-blog__txt">
                <span class="rv-recent-blog__date"><i class="fa-light fa-calendar-alt"></i>
                    {{ $post->created_at->format('d M Y') }}</span>
                <h4 class="rv-recent-blog__title"><a
                        href="{{ route('blog.detail', $post->slug) }}">{{ Str::limit($post->title, 50) }}</a>
                </h4>
            </div>
        </div>
    @endforeach
</div>
