@extends('layouts.shop')

@php
    use App\Support\JalaliDate;
@endphp

@section('content')
    <div class="fs-catalog-page wrap">
        <nav class="fs-catalog-breadcrumb" aria-label="مسیر">
            <a href="{{ route('home') }}">خانه</a>
            <span aria-hidden="true">/</span>
            <a href="{{ route('products.index') }}">فروشگاه</a>
            @if ($category)
                <span aria-hidden="true">/</span>
                <span>{{ $category->name }}</span>
            @endif
        </nav>

        <header class="fs-catalog-head">
            <h1>{{ $category?->name ?? 'همه محصولات' }}</h1>
            <p class="fs-catalog-count">{{ JalaliDate::toPersianDigits((string) $products->total()) }} محصول</p>
        </header>

        <form method="get" class="fs-catalog-toolbar">
            @if ($category)
                <input type="hidden" name="category" value="{{ $category->slug }}" />
            @endif
            @if (! empty($search))
                <input type="hidden" name="q" value="{{ $search }}" />
                <p class="fs-catalog-search-note">نتایج جستجو: «{{ $search }}»</p>
            @endif
            <div class="fs-catalog-toolbar-group">
                <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="حداقل قیمت" class="fs-catalog-input" />
                <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="حداکثر قیمت" class="fs-catalog-input" />
                <button type="submit" class="fs-catalog-apply">اعمال فیلتر</button>
            </div>
            <label class="fs-catalog-sort">
                <span>مرتب‌سازی</span>
                <select name="sort" onchange="this.form.submit()">
                    <option value="newest" @selected($sort === 'newest')>جدیدترین</option>
                    <option value="price_asc" @selected($sort === 'price_asc')>ارزان‌ترین</option>
                    <option value="price_desc" @selected($sort === 'price_desc')>گران‌ترین</option>
                    <option value="name" @selected($sort === 'name')>نام</option>
                </select>
            </label>
        </form>

        <div class="fs-catalog-grid" aria-live="polite">
            @forelse ($products as $product)
                <x-shop.catalog-product-card :product="$product" />
            @empty
                <p class="fs-catalog-empty">محصولی در این دسته یافت نشد.</p>
            @endforelse
        </div>

        @if ($products->hasPages())
            <div class="fs-catalog-pagination">
                {{ $products->links() }}
            </div>
        @endif

        @if ($category && filled($category->description))
            <section class="fs-catalog-seo fs-product-prose" aria-label="درباره دسته">
                {!! $category->description !!}
            </section>
        @endif
    </div>
@endsection