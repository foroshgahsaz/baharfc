@extends('layouts.shop')

@section('content')
    <article class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        <a href="{{ route('blog.index') }}" class="text-sm text-brand-600 hover:underline">بازگشت به بلاگ</a>
        @if ($post->category)
            <p class="mt-4 text-sm font-medium text-brand-600">{{ $post->category->name }}</p>
        @endif
        <h1 class="mt-2 text-3xl font-bold text-slate-900">{{ $post->title }}</h1>
        <p class="mt-2 text-sm text-slate-400">{{ $post->published_at?->format('Y/m/d H:i') }}</p>
        <div class="prose prose-slate mt-8 max-w-none text-sm leading-relaxed">
            {!! nl2br(e($post->body)) !!}
        </div>
    </article>
@endsection
