@extends('layouts.shop')

@section('content')
    <x-shop.home-category-strip />
    <x-shop.home-hero :banners="$banners" />

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

    <section class="border-t border-slate-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <livewire:shop.newsletter-subscribe />
        </div>
    </section>
@endsection