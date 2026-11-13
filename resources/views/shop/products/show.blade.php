@extends('layouts.shop')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <nav class="mb-6 text-sm text-slate-500">
            <a href="{{ route('home') }}" class="hover:text-brand-600">خانه</a>
            <span class="mx-2">/</span>
            <a href="{{ route('products.index') }}" class="hover:text-brand-600">محصولات</a>
            @if ($product->category)
                <span class="mx-2">/</span>
                <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="hover:text-brand-600">{{ $product->category->name }}</a>
            @endif
            <span class="mx-2">/</span>
            <span class="text-slate-800">{{ $product->name }}</span>
        </nav>

        <div class="grid gap-10 lg:grid-cols-2">
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white">
                <div class="aspect-square bg-slate-100">
                    @if ($product->imageUrl())
                        <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="h-full w-full object-cover" />
                    @else
                        <div class="flex h-full items-center justify-center text-6xl text-slate-300">📦</div>
                    @endif
                </div>
            </div>

            <div>
                @if ($product->brand)
                    <p class="text-sm font-medium text-brand-600">{{ $product->brand->name }}</p>
                @endif
                <h1 class="mt-2 text-3xl font-bold text-slate-900">{{ $product->name }}</h1>
                @if ($product->short_description)
                    <p class="mt-4 text-slate-600">{{ $product->short_description }}</p>
                @endif

                <div class="mt-6 flex flex-wrap items-end gap-3">
                    <p class="text-3xl font-bold text-brand-700">{{ $product->formattedPrice() }}</p>
                    @if ($product->hasDiscount())
                        <p class="text-lg text-slate-400 line-through">{{ number_format($product->compare_at_price) }} تومان</p>
                        <span class="rounded-lg bg-red-100 px-2 py-1 text-xs font-semibold text-red-700">{{ $product->discountPercent() }}٪</span>
                    @endif
                </div>

                <div class="mt-8 flex flex-wrap items-center gap-4">
                    <livewire:shop.add-to-cart :product-id="$product->id" :key="'add-'.$product->id" />
                    <livewire:shop.product-wishlist-toggle :product-id="$product->id" :key="'wish-'.$product->id" />
                </div>

                @if ($product->description)
                    <div class="prose prose-slate mt-10 max-w-none text-sm leading-relaxed">
                        {!! $product->description !!}
                    </div>
                @endif
            </div>
        </div>

        @if ($related->isNotEmpty())
            <section class="mt-16 border-t border-slate-200 pt-12">
                <h2 class="mb-6 text-xl font-bold text-slate-900">محصولات مرتبط</h2>
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($related as $item)
                        <x-shop.product-card :product="$item" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection
