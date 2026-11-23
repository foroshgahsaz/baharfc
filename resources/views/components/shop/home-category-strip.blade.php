@props(['homeCategories' => collect()])

@php
    $items = $homeCategories->isNotEmpty()
        ? $homeCategories
        : collect(config('shop.demo_category_icons', []));
    $progressSections = 5;
@endphp

<section class="fs-category-carousel" data-slider aria-label="دسته‌بندی محصولات">
    <header class="fs-category-carousel-heading">
        <div class="fs-category-carousel-title">
            <h2>دسته بندی محصولات</h2>
            <p>با محصولات ما بروز باشید</p>
            <div class="fs-category-carousel-arrows">
                <button type="button" data-slider-prev aria-label="قبلی" class="fs-slider-arrow">
                    <svg class="fs-chevron-icon" aria-hidden="true" viewBox="0 0 24 24"><path d="M9 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <button type="button" data-slider-next aria-label="بعدی" class="fs-slider-arrow">
                    <svg class="fs-chevron-icon" aria-hidden="true" viewBox="0 0 24 24"><path d="M15 6l-6 6 6 6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
        </div>
        <a href="{{ route('products.index') }}" class="fs-category-carousel-link">
            <strong>محـصــــولات</strong>
            <span>مشاهده همه</span>
        </a>
        <div class="fs-category-carousel-progress" data-slider-progress>
            @for ($i = 0; $i < $progressSections; $i++)
                <button
                    type="button"
                    data-slider-dot
                    aria-label="بخش {{ $i + 1 }} اسلایدر"
                    @if ($i === 0) data-active aria-current="true" @endif
                ></button>
            @endfor
        </div>
    </header>

    <div class="rail fs-category-carousel-track" data-slider-track>
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
            <a class="fs-category-carousel-card" href="{{ $href }}" aria-label="{{ $label }}">
                <span class="fs-category-carousel-art" aria-hidden="true"></span>
                <img
                    src="{{ $image }}"
                    alt=""
                    width="152"
                    height="152"
                    loading="lazy"
                    decoding="async"
                    class="fs-category-carousel-image"
                >
                <span class="fs-category-carousel-name">{{ $label }}</span>
                @if ($count !== null)
                    <span class="fs-category-carousel-count">
                        <bdi>{{ \App\Support\JalaliDate::toPersianDigits($count) }}+</bdi>
                        محصول
                    </span>
                @endif
            </a>
        @endforeach
    </div>

    <div class="fs-mobile-slider-dots">
        @for ($i = 0; $i < $progressSections; $i++)
            <button
                type="button"
                data-slider-dot
                aria-label="بخش {{ $i + 1 }} اسلایدر"
                @if ($i === 0) data-active aria-current="true" @endif
            ></button>
        @endfor
    </div>
</section>