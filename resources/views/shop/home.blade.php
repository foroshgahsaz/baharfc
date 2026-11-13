@extends('layouts.shop')

@section('content')
    @if ($banners->isNotEmpty())
        <section class="bg-slate-900">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach ($banners as $banner)
                        <a
                            href="{{ $banner->link_url ?: route('products.index') }}"
                            class="group relative overflow-hidden rounded-3xl bg-gradient-to-l from-brand-800 to-brand-600 p-8 text-white shadow-lg"
                        >
                            @if ($banner->image_url)
                                <img src="{{ $banner->image_url }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-40" />
                            @endif
                            <div class="relative">
                                <h2 class="text-2xl font-bold">{{ $banner->title }}</h2>
                                @if ($banner->subtitle)
                                    <p class="mt-2 text-sm text-brand-100">{{ $banner->subtitle }}</p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="relative overflow-hidden bg-gradient-to-bl from-brand-700 via-brand-600 to-teal-500 text-white">
        <div class="absolute -left-24 top-10 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute -bottom-16 right-0 h-72 w-72 rounded-full bg-teal-300/20 blur-3xl"></div>
        <div class="relative mx-auto grid max-w-7xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:items-center lg:px-8 lg:py-24">
            <div>
                <p class="text-sm font-medium text-brand-100">خوش آمدید</p>
                <h1 class="mt-3 text-4xl font-bold leading-tight sm:text-5xl">
                    خرید آنلاین با تجربه‌ای سریع و مدرن
                </h1>
                <p class="mt-5 max-w-xl text-base leading-relaxed text-brand-50/90">
                    این نسخه پایه فروشگاه شماست — آماده اتصال به حسابداری، دیجی‌پی، اسنپ‌پی و درگاه‌های بانکی.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="#products" class="rounded-2xl bg-white px-6 py-3 text-sm font-semibold text-brand-800 shadow-lg shadow-brand-900/20 transition hover:bg-brand-50">
                        مشاهده محصولات
                    </a>
                    @guest
                        <a href="{{ route('login') }}" class="rounded-2xl border border-white/40 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                            ورود مشتریان
                        </a>
                    @endguest
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                @foreach ([
                    ['label' => 'ارسال سریع', 'value' => '۲۴–۷۲ ساعته'],
                    ['label' => 'پشتیبانی', 'value' => '۷ روز هفته'],
                    ['label' => 'پرداخت امن', 'value' => 'درگاه‌های معتبر'],
                    ['label' => 'مرجوعی', 'value' => 'طبق قوانین فروشگاه'],
                ] as $item)
                    <div class="rounded-2xl border border-white/20 bg-white/10 p-4 backdrop-blur-sm">
                        <p class="text-xs text-brand-100">{{ $item['label'] }}</p>
                        <p class="mt-1 text-sm font-semibold">{{ $item['value'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

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
