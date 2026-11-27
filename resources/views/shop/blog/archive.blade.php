@extends('layouts.shop')

@section('content')
    <div class="fs-blog-page wrap">
        <nav class="fs-blog-breadcrumb" aria-label="مسیر">
            <a href="{{ route('home') }}">خانه</a>
            <span>/</span>
            <a href="{{ route('blog.index') }}">بلاگ</a>
            <span>/</span>
            <span>{{ $archiveHeading }}</span>
        </nav>

        <header class="fs-blog-page-head">
            <h1>{{ $archiveHeading }}</h1>
            @if (! empty($archiveIntro))
                <p>{{ $archiveIntro }}</p>
            @endif
            @if (! empty($searchQuery))
                <form method="get" action="{{ route('blog.search') }}" class="fs-blog-inline-search">
                    <input type="search" name="q" value="{{ $searchQuery }}" />
                    <button type="submit">جستجو</button>
                </form>
            @endif
        </header>

        <div class="fs-blog-layout">
            <div class="fs-blog-main">
                <div class="fs-blog-grid">
                    @forelse ($posts as $post)
                        <x-shop.blog-post-card :post="$post" />
                    @empty
                        <p class="fs-blog-empty">مطلبی یافت نشد.</p>
                    @endforelse
                </div>
                <div class="fs-blog-pagination">{{ $posts->links() }}</div>
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