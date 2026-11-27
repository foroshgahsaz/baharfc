@props([
    'sidebarCategories' => collect(),
    'popularTags' => collect(),
    'recentPosts' => collect(),
    'archiveMonths' => collect(),
])

<aside class="fs-blog-sidebar" aria-label="ابزارک‌های بلاگ">
    <form method="get" action="{{ route('blog.search') }}" class="fs-blog-sidebar-search">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="جستجو در بلاگ..." aria-label="جستجو در بلاگ" />
        <button type="submit">جستجو</button>
    </form>

    <section class="fs-blog-widget">
        <h2>دسته‌ها</h2>
        <ul>
            <li><a href="{{ route('blog.index') }}">همه مطالب</a></li>
            @foreach ($sidebarCategories as $category)
                <li>
                    <a href="{{ route('blog.category', $category->slug) }}">
                        {{ $category->name }}
                        <span class="fs-blog-widget-count">{{ $category->published_posts_count }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </section>

    @if ($recentPosts->isNotEmpty())
        <section class="fs-blog-widget">
            <h2>تازه‌ها</h2>
            <ul class="fs-blog-widget-recent">
                @foreach ($recentPosts as $recent)
                    <li>
                        <a href="{{ route('blog.show', $recent->slug) }}">{{ $recent->title }}</a>
                        <time datetime="{{ $recent->published_at?->toDateString() }}">@jalaliDate($recent->published_at)</time>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    @if ($popularTags->isNotEmpty())
        <section class="fs-blog-widget">
            <h2>برچسب‌ها</h2>
            <div class="fs-blog-tag-cloud">
                @foreach ($popularTags as $tag)
                    <a href="{{ route('blog.tag', $tag->slug) }}">{{ $tag->name }}</a>
                @endforeach
            </div>
        </section>
    @endif

    @if ($archiveMonths->isNotEmpty())
        <section class="fs-blog-widget">
            <h2>آرشیو</h2>
            <ul>
                @foreach ($archiveMonths as $row)
                    <li>
                        <a href="{{ route('blog.archive', ['year' => $row->year, 'month' => $row->month]) }}">
                            {{ $row->year }}/{{ sprintf('%02d', $row->month) }}
                            <span class="fs-blog-widget-count">{{ $row->total }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    <p class="fs-blog-rss">
        <a href="{{ route('blog.feed') }}">فید RSS</a>
    </p>
</aside>