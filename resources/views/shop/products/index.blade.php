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
                @if ($category->parent)
                    <span aria-hidden="true">/</span>
                    <a href="{{ route('categories.show', $category->parent->slug) }}">{{ $category->parent->name }}</a>
                @endif
                <span aria-hidden="true">/</span>
                <span>{{ $category->name }}</span>
            @endif
        </nav>

        <header class="fs-catalog-head">
            <h1>{{ $category?->name ?? 'همه محصولات' }}</h1>
            <p class="fs-catalog-count">{{ JalaliDate::toPersianDigits((string) $products->total()) }} محصول</p>
        </header>

        <div class="fs-catalog-backdrop" id="catalogFiltersBackdrop" aria-hidden="true" hidden></div>

        <div class="fs-catalog-layout">
            <div class="fs-catalog-main">
                @if (! empty($search))
                    <p class="fs-catalog-search-note">نتایج جستجو: «{{ $search }}»</p>
                @endif

                <button type="button" class="fs-catalog-mobile-filters" data-catalog-filters-open aria-expanded="false">
                    فیلتر و دسته‌بندی
                </button>

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

            <aside class="fs-catalog-sidebar" id="catalogSidebar" aria-label="فیلترها" aria-hidden="true">
                <div class="fs-catalog-sidebar-head">
                    <strong>فیلترها</strong>
                    <button type="button" class="fs-catalog-sidebar-close" data-catalog-filters-close aria-label="بستن فیلترها">×</button>
                </div>
                <form method="get" action="{{ $formAction }}" class="fs-catalog-filters">
                    @if (! empty($search))
                        <input type="hidden" name="q" value="{{ $search }}" />
                    @endif

                    <details class="fs-filter-block" open>
                        <summary>دسته‌بندی‌ها</summary>
                        <ul class="fs-filter-list">
                            <li>
                                <a href="{{ route('products.index', array_filter(['q' => $search ?: null])) }}" @class(['is-active' => ! $category])>همه محصولات</a>
                            </li>
                            @foreach ($categories as $cat)
                                <li>
                                    <a
                                        href="{{ route('categories.show', $cat->slug) }}"
                                        @class(['is-active' => $category && $category->id === $cat->id])
                                    >{{ $cat->name }}
                                        <span class="fs-filter-count">{{ JalaliDate::toPersianDigits((string) ($cat->products_count ?? 0)) }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </details>

                    @if ($sidebarChildCategories->isNotEmpty())
                        <details class="fs-filter-block" open>
                            <summary>زیردسته‌ها</summary>
                            <ul class="fs-filter-list">
                                @foreach ($sidebarChildCategories as $child)
                                    <li>
                                        <a
                                            href="{{ route('categories.show', $child->slug) }}"
                                            @class(['is-active' => $category && $category->id === $child->id])
                                        >{{ $child->name }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </details>
                    @endif

                    @if ($brands->isNotEmpty())
                        <details class="fs-filter-block" open>
                            <summary>برند</summary>
                            <ul class="fs-filter-list">
                                @php
                                    $brandQueryBase = array_filter([
                                        'q' => $search ?: null,
                                        'min_price' => request('min_price'),
                                        'max_price' => request('max_price'),
                                        'sort' => $sort !== 'newest' ? $sort : null,
                                    ]);
                                @endphp
                                <li>
                                    <a
                                        href="{{ $category ? route('categories.show', array_merge(['slug' => $category->slug], $brandQueryBase)) : route('products.index', $brandQueryBase) }}"
                                        @class(['is-active' => ! $selectedBrand])
                                    >همه برندها</a>
                                </li>
                                @foreach ($brands as $brand)
                                    <li>
                                        <a
                                            href="{{ $category ? route('categories.show', array_merge(['slug' => $category->slug, 'brand' => $brand->slug], $brandQueryBase)) : route('products.index', array_merge(['brand' => $brand->slug], $brandQueryBase)) }}"
                                            @class(['is-active' => $selectedBrand && $selectedBrand->id === $brand->id])
                                        >{{ $brand->name }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </details>
                    @endif

                    <details class="fs-filter-block" open>
                        <summary>محدوده قیمت</summary>
                        <div class="fs-filter-price">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="حداقل" class="fs-catalog-input" />
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="حداکثر" class="fs-catalog-input" />
                        </div>
                        <label class="fs-catalog-sort fs-catalog-sort--sidebar">
                            <span>مرتب‌سازی</span>
                            <select name="sort" onchange="this.form.submit()">
                                <option value="newest" @selected($sort === 'newest')>جدیدترین</option>
                                <option value="price_asc" @selected($sort === 'price_asc')>ارزان‌ترین</option>
                                <option value="price_desc" @selected($sort === 'price_desc')>گران‌ترین</option>
                                <option value="name" @selected($sort === 'name')>نام</option>
                            </select>
                        </label>
                        <button type="submit" class="fs-catalog-apply fs-catalog-apply--block">اعمال فیلتر</button>
                    </details>
                </form>
            </aside>
        </div>
    </div>
@endsection