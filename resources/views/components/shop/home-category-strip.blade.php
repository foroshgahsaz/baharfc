@props(['homeCategories' => collect()])

<section class="categories" aria-label="دسته‌بندی‌های محبوب">
    <a class="category-promo" href="{{ route('products.index', ['sort' => 'newest']) }}">
        <strong>با جدیدها</strong>
        <b>{{ $shopWordmark ?? config('shop.logo_wordmark', 'BAHARFC') }}</b>
        <span aria-hidden="true">‹‹‹</span>
    </a>

    <div class="category-list" id="categoryList">
        @forelse ($homeCategories as $category)
            <a
                class="category"
                href="{{ route('products.index', ['category' => $category->slug]) }}"
                data-name="{{ $category->name }}"
            >
                @if ($category->imageUrl())
                    <img src="{{ $category->imageUrl() }}" alt="" width="82" height="82" loading="lazy" decoding="async">
                @else
                    <img src="{{ asset('images/lebasino/categories/cat-1.jpg') }}" alt="" width="82" height="82" loading="lazy">
                @endif
                <span>{!! nl2br(e($category->home_caption ?: $category->name)) !!}</span>
            </a>
        @empty
            @foreach (config('shop.demo_category_icons', []) as $item)
                <a class="category" href="{{ route('products.index', ['category' => $item['slug']]) }}" data-name="{{ $item['label'] }}">
                    <img src="{{ asset($item['image']) }}" alt="" width="82" height="82" loading="lazy">
                    <span>{!! nl2br(e($item['label'])) !!}</span>
                </a>
            @endforeach
        @endforelse
    </div>
</section>