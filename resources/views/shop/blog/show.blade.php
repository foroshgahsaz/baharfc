@extends('layouts.shop')

@push('head')
    <link rel="canonical" href="{{ route('blog.show', $post->slug) }}">
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{ $post->meta_title ?: $post->title }}">
    <meta property="og:description" content="{{ $post->metaDescriptionForSeo() }}">
    <meta property="og:url" content="{{ route('blog.show', $post->slug) }}">
    @if ($post->featuredImageUrl())
        <meta property="og:image" content="{{ $post->featuredImageUrl() }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">
@endpush

@section('content')
    <article class="fs-blog-single wrap" itemscope itemtype="https://schema.org/BlogPosting">
        <nav class="fs-blog-breadcrumb" aria-label="مسیر">
            <a href="{{ route('home') }}">خانه</a>
            <span>/</span>
            <a href="{{ route('blog.index') }}">بلاگ</a>
            @if ($post->category)
                <span>/</span>
                <a href="{{ route('blog.category', $post->category->slug) }}">{{ $post->category->name }}</a>
            @endif
        </nav>

        <div class="fs-blog-layout">
            <div class="fs-blog-main">
                <header class="fs-blog-single-head">
                    @if ($post->category)
                        <a href="{{ route('blog.category', $post->category->slug) }}" class="fs-blog-card-category">{{ $post->category->name }}</a>
                    @endif
                    <h1 itemprop="headline">{{ $post->title }}</h1>
                    <p class="fs-blog-card-meta">
                        <time itemprop="datePublished" datetime="{{ $post->published_at?->toAtomString() }}">@jalali($post->published_at)</time>
                        @if ($post->author)
                            <span>·</span>
                            <a href="{{ route('blog.author', $post->author) }}"><span itemprop="author">{{ $post->author->name }}</span></a>
                        @endif
                        <span>·</span>
                        <span>{{ $post->readingTimeMinutes() }} دقیقه مطالعه</span>
                    </p>
                </header>

                @if ($post->featuredImageUrl())
                    <img
                        src="{{ $post->featuredImageUrl() }}"
                        alt=""
                        class="fs-blog-single-cover"
                        itemprop="image"
                        loading="lazy"
                    />
                @endif

                @if ($post->excerpt)
                    <p class="fs-blog-single-excerpt" itemprop="description">{{ $post->excerpt }}</p>
                @endif

                <div class="fs-blog-single-content fs-product-prose" itemprop="articleBody">
                    {!! $post->renderedBody() !!}
                </div>

                @if ($post->relationLoaded('tags') && $post->tags->isNotEmpty())
                    <div class="fs-blog-single-tags">
                        <span>برچسب‌ها:</span>
                        @foreach ($post->tags as $tag)
                            <a href="{{ route('blog.tag', $tag->slug) }}">{{ $tag->name }}</a>
                        @endforeach
                    </div>
                @endif

                @if ($post->allow_comments)
                    <livewire:shop.blog-post-comments :post="$post" />
                @else
                    <p class="fs-blog-comments-closed">ثبت نظر برای این مطلب بسته است.</p>
                @endif

                @if ($related->isNotEmpty())
                    <section class="fs-blog-related" aria-labelledby="related-posts">
                        <h2 id="related-posts">مطالب مرتبط</h2>
                        <div class="fs-blog-related-grid">
                            @foreach ($related as $item)
                                <x-shop.blog-post-card :post="$item" />
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>

            <x-shop.blog-sidebar
                :sidebar-categories="$sidebarCategories"
                :popular-tags="$popularTags"
                :recent-posts="$recentPosts"
                :archive-months="$archiveMonths"
            />
        </div>
    </article>
@endsection