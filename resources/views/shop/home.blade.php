@extends('layouts.shop')

@section('content')
    <x-shop.home-carousels-after anchor="start" :grouped="$homeCarousels" />
    <x-shop.home-hero :banners="$banners" />
    <x-shop.home-carousels-after anchor="hero" :grouped="$homeCarousels" />
    <x-shop.home-category-strip :home-categories="$homeCategories" />
    <x-shop.home-carousels-after anchor="category_strip" :grouped="$homeCarousels" />
    <x-shop.home-special-deals :special-deal-products="$specialDealProducts" />
    <x-shop.home-carousels-after anchor="special_deals" :grouped="$homeCarousels" />

    <section id="products" class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
        <div class="mb-8 flex items-end justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">محصولات ویژه</h2>
                <p class="mt-1 text-sm text-slate-500">از پنل ادمین قابل مدیریت است</p>
            </div>
        </div>
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @forelse ($featuredProducts as $product)
                <x-shop.product-card :product="$product" />
            @empty
                <p class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-slate-500">
                    هنوز محصولی ثبت نشده. از پنل ادمین محصول اضافه کنید.
                </p>
            @endforelse
        </div>
    </section>

    <x-shop.home-carousels-after anchor="featured" :grouped="$homeCarousels" />

    @if ($latestProducts->isNotEmpty())
        <section class="border-t border-slate-200 bg-white">
            <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
                <h2 class="mb-8 text-2xl font-bold text-slate-900">جدیدترین‌ها</h2>
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($latestProducts as $product)
                        <x-shop.product-card :product="$product" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <x-shop.home-carousels-after anchor="latest" :grouped="$homeCarousels" />

    <section class="border-t border-slate-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <livewire:shop.newsletter-subscribe />
        </div>
    </section>

    <x-shop.home-carousels-after anchor="newsletter" :grouped="$homeCarousels" />
@endsection