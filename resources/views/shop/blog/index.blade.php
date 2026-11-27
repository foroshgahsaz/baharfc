@extends('layouts.shop')

@section('content')
    <div class="fs-blog-page wrap">
        <header class="fs-blog-page-head">
            <h1>بلاگ</h1>
            <p>مقالات و راهنماهای فروشگاه</p>
        </header>

        <div class="fs-blog-layout">
            <div class="fs-blog-main">
                @if ($featuredPosts->isNotEmpty())
                    <section class="fs-blog-featured" aria-label="مطالب ویژه">
                        <h2>مطالب ویژه</h2>
                        <div class="fs-blog-featured-grid">
                            @foreach ($featuredPosts as $post)
                                <x-shop.blog-post-card :post="$post" :featured="true" />
                            @endforeach
                        </div>
                    </section>
                @endif

                <section aria-labelledby="blog-latest-heading">
                    <h2 id="blog-latest-heading" class="fs-blog-section-title">{{ $archiveHeading ?? 'آخرین مطالب' }}</h2>
                    <div class="fs-blog-grid">
                        @forelse ($posts as $post)
                            <x-shop.blog-post-card :post="$post" />
                        @empty
                            <p class="fs-blog-empty">هنوز مطلبی منتشر نشده است.</p>
                        @endforelse
                    </div>
                    <div class="fs-blog-pagination">{{ $posts->links() }}</div>
                </section>
            </div>

            <x-shop.blog-sidebar
                :sidebar-categories="$sidebarCategories"
                :popular-tags="$popularTags"
                :recent-posts="$recentPosts"
                :archive-months="$archiveMonths"
            />
        </div>
    </div>
@endsection