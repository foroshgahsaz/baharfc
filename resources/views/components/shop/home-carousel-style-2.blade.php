@props(['section', 'products'])

@php
    use App\Support\JalaliDate;
    $dotCount = min(5, max(1, (int) ceil($products->count() / 2)));
@endphp

<section class="home-carousel home-carousel-style-2" data-slider aria-label="{{ $section->title }}">
    <header class="home-carousel-style-2-heading">
        <div class="home-carousel-style-2-title-wrap">
            <h2>
                @if ($section->subtitle)
                    <span class="home-carousel-style-2-highlight">{{ $section->subtitle }}</span>
                @endif
                {{ $section->title }}
            </h2>
            <span class="home-carousel-style-2-line" aria-hidden="true"></span>
        </div>
        <div class="home-carousel-style-2-controls">
            <a href="{{ $section->moreLinkUrl() }}" class="home-carousel-style-2-brands">{{ $section->moreLinkLabel() }}</a>
            <div class="home-carousel-style-2-arrows">
                <button type="button" data-slider-prev aria-label="قبلی" class="home-carousel-style-2-arrow">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                </button>
                <button type="button" data-slider-next aria-label="بعدی" class="home-carousel-style-2-arrow">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
            </div>
        </div>
    </header>

    <div class="rail home-carousel-style-2-track" data-slider-track>
        @foreach ($products as $product)
            <article class="home-carousel-style-2-card">
                <a href="{{ route('products.show', $product->slug) }}" class="home-carousel-style-2-image">
                    @if ($product->imageUrl())
                        <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" loading="lazy" decoding="async" width="220" height="280">
                    @else
                        <span class="home-carousel-style-2-placeholder">📦</span>
                    @endif
                    <span class="home-carousel-style-2-swatches" aria-hidden="true"><i></i><i></i><i></i></span>
                </a>
                <h3 class="home-carousel-style-2-name">
                    <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
                </h3>
                <footer class="home-carousel-style-2-footer">
                    <div class="home-carousel-style-2-price">
                        @if (! $product->is_active)
                            <span class="home-carousel-style-2-out">ناموجود</span>
                        @else
                            @if ($product->hasDiscount())
                                <span class="home-carousel-style-2-percent">{{ JalaliDate::toPersianDigits($product->discountPercent()) }}٪</span>
                                <span class="home-carousel-style-2-old">{{ JalaliDate::toPersianDigits(number_format($product->compare_at_price)) }}</span>
                            @endif
                            <span class="home-carousel-style-2-final">{{ JalaliDate::toPersianDigits(number_format($product->sellablePrice())) }} تومان</span>
                        @endif
                    </div>
                    <a href="{{ route('products.show', $product->slug) }}" class="home-carousel-style-2-cart" aria-label="مشاهده {{ $product->name }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 6h15l-1.5 9H7.5L6 6z"/><path d="M6 6l-1-2H2"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg>
                    </a>
                </footer>
            </article>
        @endforeach
    </div>

    <div class="home-carousel-style-2-dots" data-slider-progress>
        @for ($i = 0; $i < $dotCount; $i++)
            <button
                type="button"
                data-slider-dot
                aria-label="بخش {{ $i + 1 }} کاروسل"
                @if ($i === 0) data-active aria-current="true" @endif
            ></button>
        @endfor
    </div>
</section>