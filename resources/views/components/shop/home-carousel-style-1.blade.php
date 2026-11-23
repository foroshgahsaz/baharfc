@props(['section', 'products'])

@php
    use App\Support\JalaliDate;
@endphp

<section class="home-carousel home-carousel-style-1" data-slider aria-label="{{ $section->title }}">
    <header class="home-carousel-style-1-heading">
        <div class="home-carousel-style-1-title">
            <span class="home-carousel-style-1-icon" aria-hidden="true">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 19h16M6 16l3-9 3 5 3-7 3 11"/></svg>
            </span>
            <div>
                <h2>{{ $section->title }}</h2>
                @if ($section->subtitle)
                    <p>{{ $section->subtitle }}</p>
                @endif
            </div>
        </div>
        <a href="{{ $section->moreLinkUrl() }}" class="home-carousel-style-1-more">
            <span>{{ $section->moreLinkLabel() }}</span>
            <span class="home-carousel-style-1-more-icon" aria-hidden="true">←</span>
        </a>
    </header>

    <div class="home-carousel-style-1-shell">
        <div class="rail home-carousel-style-1-track" data-slider-track>
            @foreach ($products as $product)
                <article class="home-carousel-style-1-card">
                    <div class="home-carousel-style-1-card-colors" aria-hidden="true">
                        <span></span><span></span><span></span>
                    </div>
                    <a href="{{ route('products.show', $product->slug) }}" class="home-carousel-style-1-image">
                        @if ($product->imageUrl())
                            <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" loading="lazy" decoding="async" width="200" height="200">
                        @else
                            <span class="home-carousel-style-1-placeholder">📦</span>
                        @endif
                    </a>
                    <h3 class="home-carousel-style-1-name">
                        <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
                    </h3>
                    <footer class="home-carousel-style-1-footer">
                        <a href="{{ route('products.show', $product->slug) }}" class="home-carousel-style-1-cart" aria-label="مشاهده {{ $product->name }}">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 6h15l-1.5 9H7.5L6 6z"/><path d="M6 6l-1-2H2"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg>
                        </a>
                        <div class="home-carousel-style-1-price">
                            @if ($product->hasDiscount())
                                <span class="home-carousel-style-1-discount" aria-label="تخفیف">
                                    {{ JalaliDate::toPersianDigits($product->discountPercent()) }}%
                                </span>
                                <span class="home-carousel-style-1-old">{{ JalaliDate::toPersianDigits(number_format($product->compare_at_price)) }}</span>
                            @endif
                            <span class="home-carousel-style-1-final">{{ JalaliDate::toPersianDigits(number_format($product->sellablePrice())) }} تومان</span>
                        </div>
                    </footer>
                </article>
            @endforeach
        </div>
        <button type="button" class="home-carousel-style-1-side-arrow home-carousel-style-1-side-arrow--prev" data-slider-prev aria-label="قبلی">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
        <button type="button" class="home-carousel-style-1-side-arrow home-carousel-style-1-side-arrow--next" data-slider-next aria-label="بعدی">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
    </div>
</section>