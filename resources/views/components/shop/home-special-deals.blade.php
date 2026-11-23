@props(['specialDealProducts' => collect()])

@php
    use App\Support\JalaliDate;
@endphp

@if ($specialDealProducts->isNotEmpty())
    <section class="amazing-section" data-amazing-deals aria-label="پیشنهاد شگفت‌انگیز">
        <div class="amazing-bg" aria-hidden="true">
            <span class="amazing-bg-strip"></span>
        </div>

        <div class="amazing-banner">
            <div class="amazing-banner-title">
                <h2>پیشنهاد</h2>
                <p>شگفت انگیز</p>
            </div>

            <div class="amazing-percent-badge" aria-hidden="true">
                <span class="amazing-percent-shape">
                    <span class="amazing-percent-symbol">%</span>
                </span>
                <span class="amazing-fire-icon">🔥</span>
            </div>

            <a href="{{ route('products.index', ['sort' => 'price_desc']) }}" class="amazing-view-more">
                <span>مشاهده بیشتر</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
            </a>
        </div>

        <div class="amazing-products">
            <div class="amazing-products-track rail" data-amazing-deals-track>
                @foreach ($specialDealProducts as $product)
                    @php
                        $hasDiscount = $product->hasDiscount();
                        $percent = $product->discountPercent();
                    @endphp
                    <article class="amazing-product-card">
                        <header class="amazing-product-card-header">
                            <a
                                href="{{ route('products.show', $product->slug) }}"
                                class="amazing-wishlist-link"
                                title="مشاهده محصول"
                                aria-label="مشاهده {{ $product->name }}"
                            >
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                </svg>
                            </a>
                            <div class="amazing-rating" aria-hidden="true">
                                <span>۰</span>
                                <span class="amazing-star">★</span>
                            </div>
                        </header>

                        <a href="{{ route('products.show', $product->slug) }}" class="amazing-product-image">
                            @if ($product->imageUrl())
                                <img
                                    src="{{ $product->imageUrl() }}"
                                    alt="{{ $product->name }}"
                                    width="190"
                                    height="125"
                                    loading="lazy"
                                    decoding="async"
                                >
                            @else
                                <span class="amazing-product-placeholder" aria-hidden="true">📦</span>
                            @endif
                        </a>

                        <h3 class="amazing-product-title">
                            <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
                        </h3>

                        <footer class="amazing-product-card-footer">
                            <a href="{{ route('products.show', $product->slug) }}" class="amazing-buy-btn">خرید</a>
                            <div class="amazing-price-box">
                                @if ($hasDiscount && $percent)
                                    <div class="amazing-discount-row">
                                        <span class="amazing-old-price">{{ JalaliDate::toPersianDigits(number_format($product->compare_at_price)) }}</span>
                                        <span class="amazing-discount-badge">٪{{ JalaliDate::toPersianDigits($percent) }}</span>
                                    </div>
                                @endif
                                <div class="amazing-final-price-row">
                                    <span class="amazing-final-price">{{ JalaliDate::toPersianDigits(number_format($product->sellablePrice())) }}</span>
                                    <span class="amazing-toman-label"><span>تو</span><span>مان</span></span>
                                </div>
                            </div>
                        </footer>
                    </article>
                @endforeach
            </div>

            <span class="amazing-products-fade" aria-hidden="true"></span>

            <div class="amazing-slider-nav">
                <button type="button" class="amazing-nav-arrow" data-amazing-deals-prev aria-label="قبلی">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>
                <button type="button" class="amazing-nav-arrow" data-amazing-deals-next aria-label="بعدی">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>
            </div>
        </div>
    </section>
@endif