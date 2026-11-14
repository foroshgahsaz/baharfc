@extends('layouts.shop')

@section('content')
    <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-slate-900">بلاگ</h1>
        <div class="mt-8 space-y-6">
            @forelse ($posts as $post)
                <article class="rounded-2xl border border-slate-200 bg-white p-6">
                    @if ($post->category)
                        <p class="text-xs font-medium text-brand-600">{{ $post->category->name }}</p>
                    @endif
                    <h2 class="mt-2 text-xl font-bold">
                        <a href="{{ route('blog.show', $post->slug) }}" class="hover:text-brand-700">{{ $post->title }}</a>
                    </h2>
                    @if ($post->excerpt)
                        <p class="mt-2 text-sm text-slate-600">{{ $post->excerpt }}</p>
                    @endif
                    <p class="mt-3 text-xs text-slate-400">{{ $post->published_at?->format('Y/m/d') }}</p>
                </article>
            @empty
                <p class="text-slate-500">هنوز مطلبی منتشر نشده است.</p>
            @endforelse
        </div>
        <div class="mt-8">{{ $posts->links() }}</div>
    </div>
@endsection
