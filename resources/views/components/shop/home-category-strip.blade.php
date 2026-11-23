@props(['homeCategories' => collect()])

@php
    $items = $homeCategories->isNotEmpty()
        ? $homeCategories
        : collect(config('shop.demo_category_icons', []))->take(8);
@endphp

<section class="home-product-categories" aria-label="دسته‌بندی محصولات">
    <header class="hpc-topbar">
        <div class="hpc-heading">
            <span class="hpc-dash" aria-hidden="true"></span>
            <span>دسته بندی محصولات</span>
        </div>
        <span class="hpc-vline" aria-hidden="true"></span>
        <div class="hpc-updates">
            <span>با محصولات ما بروز باشید</span>
            <div class="hpc-arrows" aria-hidden="true">
                <span class="hpc-arrow">‹</span>
                <span class="hpc-arrow">›</span>
            </div>
        </div>
        <div class="hpc-spacer"></div>
        <div class="hpc-tools">
            <div class="hpc-dots" aria-hidden="true">
                <span class="hpc-dot"></span>
                <span class="hpc-dot"></span>
                <span class="hpc-dot is-active"></span>
                <span class="hpc-dot"></span>
                <span class="hpc-dot"></span>
            </div>
            <a class="hpc-view-all" href="{{ route('products.index') }}">
                محصولات
                <small>مشاهده همه</small>
            </a>
        </div>
    </header>

    <div class="hpc-grid">
        @foreach ($items as $item)
            @php
                $isModel = $item instanceof \App\Models\Category;
                $label = $isModel ? ($item->home_caption ?: $item->name) : ($item['label'] ?? '');
                $href = route('products.index', ['category' => $isModel ? $item->slug : ($item['slug'] ?? null)]);
                $fallbackImage = 'images/lebasino/categories/cat-'.(($loop->index % 10) + 1).'.jpg';
                $image = $isModel
                    ? ($item->imageUrl() ?: asset($fallbackImage))
                    : asset($item['image'] ?? $fallbackImage);
                $count = $isModel ? (int) ($item->products_count ?? 0) : null;
            @endphp
            <a class="hpc-item" href="{{ $href }}" aria-label="{{ $label }}">
                <div class="hpc-visual">
                    <img src="{{ $image }}" alt="" width="152" height="152" loading="lazy" decoding="async">
                </div>
                <div class="hpc-name">{{ $label }}</div>
                @if ($count !== null)
                    <div class="hpc-count">
                        <span class="hpc-number">{{ \App\Support\JalaliDate::toPersianDigits($count) }}+</span>
                        محصول
                    </div>
                @endif
            </a>
        @endforeach
    </div>
</section>
