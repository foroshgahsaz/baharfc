@props(['section', 'products'])

@php
    use App\Support\JalaliDate;
@endphp

<section class="home-carousel home-carousel-style-1" data-slider aria-label="{{ $section->title }}">
    <header class="home-carousel-style-1-heading">
        <div class="home-carousel-style-1-title">
            <span class="home-carousel-style-1-icon" aria-hidden="true">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 19v-3"/>
                    <path d="M18 19v-3"/>
                    <path d="M4 11V8a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3"/>
                    <path d="M5 11h14v6a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-6z"/>
                </svg>
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
            <span class="home-carousel-style-1-more-icon" aria-hidden="true">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            </span>
        </a>
    </header>

    <div class="home-carousel-style-1-shell">
        <button type="button" class="home-carousel-style-1-side-arrow home-carousel-style-1-side-arrow--prev" data-slider-prev aria-label="قبلی">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="15 18 9 12 15 6"/></svg>
        </button>

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
                        <div class="home-carousel-style-1-price">
                            @if ($product->hasDiscount())
                                <div class="home-carousel-style-1-discount-row">
                                    <span class="home-carousel-style-1-old">{{ JalaliDate::toPersianDigits(number_format($product->compare_at_price)) }}</span>
                                    <span class="home-carousel-style-1-discount">{{ JalaliDate::toPersianDigits($product->discountPercent()) }}%</span>
                                </div>
                            @endif
                            <span class="home-carousel-style-1-final">{{ JalaliDate::toPersianDigits(number_format($product->sellablePrice())) }} تومان</span>
                        </div>
                        <a href="{{ route('products.show', $product->slug) }}" class="home-carousel-style-1-cart" aria-label="مشاهده {{ $product->name }}">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4H6z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                        </a>
                    </footer>
                </article>
            @endforeach
        </div>

        <button type="button" class="home-carousel-style-1-side-arrow home-carousel-style-1-side-arrow--next" data-slider-next aria-label="بعدی">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
    </div>
</section>