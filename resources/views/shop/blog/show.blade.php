@extends('layouts.shop')

@section('content')
    <article class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        <a href="{{ route('blog.index') }}" class="text-sm text-brand-600 hover:underline">بازگشت به بلاگ</a>
        @if ($post->category)
            <p class="mt-4 text-sm font-medium text-brand-600">{{ $post->category->name }}</p>
        @endif
        <h1 class="mt-2 text-3xl font-bold text-slate-900">{{ $post->title }}</h1>
        <p class="mt-2 text-sm text-slate-400">
            @jalali($post->published_at)
            @if ($post->author)
                <span class="mx-2">·</span>
                <span>{{ $post->author->name }}</span>
            @endif
        </p>
        @if ($post->featuredImageUrl())
            <img
                src="{{ $post->featuredImageUrl() }}"
                alt=""
                class="mt-6 w-full rounded-2xl object-cover"
                loading="lazy"
            />
        @endif
        @if ($post->excerpt)
            <p class="mt-6 text-lg text-slate-600">{{ $post->excerpt }}</p>
        @endif
        <div class="prose prose-slate mt-8 max-w-none text-sm leading-relaxed">
            {!! $post->renderedBody() !!}
        </div>

        @if ($post->tags)
            <p class="mt-8 text-xs text-slate-500">
                <span class="font-medium">برچسب‌ها:</span>
                {{ $post->tags }}
            </p>
        @endif

        <livewire:shop.blog-post-comments :post="$post" />
    </article>
@endsection