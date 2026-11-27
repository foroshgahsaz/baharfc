@props(['post', 'featured' => false])

<article @class(['fs-blog-card', 'fs-blog-card--featured' => $featured])>
    <a href="{{ route('blog.show', $post->slug) }}" class="fs-blog-card-media" tabindex="-1" aria-hidden="true">
        @if ($post->featuredImageUrl())
            <img src="{{ $post->featuredImageUrl() }}" alt="" loading="lazy" decoding="async" />
        @else
            <span class="fs-blog-card-placeholder">✎</span>
        @endif
    </a>
    <div class="fs-blog-card-body">
        @if ($post->category)
            <a href="{{ route('blog.category', $post->category->slug) }}" class="fs-blog-card-category">{{ $post->category->name }}</a>
        @endif
        <h2 class="fs-blog-card-title">
            <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
        </h2>
        @if ($post->excerpt)
            <p class="fs-blog-card-excerpt">{{ $post->excerpt }}</p>
        @endif
        <p class="fs-blog-card-meta">
            <time datetime="{{ $post->published_at?->toAtomString() }}">@jalaliDate($post->published_at)</time>
            @if ($post->author)
                <span>·</span>
                <a href="{{ route('blog.author', $post->author) }}">{{ $post->author->name }}</a>
            @endif
            <span>·</span>
            <span>{{ $post->readingTimeMinutes() }} دقیقه مطالعه</span>
        </p>
    </div>
</article>