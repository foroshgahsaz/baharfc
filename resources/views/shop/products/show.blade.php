@extends('layouts.shop')

@php
    use App\Support\JalaliDate;
    use App\Support\ProductPagePresenter;
    use App\Support\ShopAppearance;

    $presenter = new ProductPagePresenter($product);
    $gallery = $presenter->galleryUrls();
    $mainImage = $gallery[0] ?? null;
    $keySpecs = $presenter->keySpecs();
    $specGroups = $presenter->specGroups();
    $colors = $presenter->colorSwatches();
    $optionPills = $presenter->optionPills();
    $optionGroupLabel = $presenter->optionGroupLabel();
    $shopName = ShopAppearance::siteName();
    $categoryTrail = collect();
    $trailCategory = $product->category;
    while ($trailCategory) {
        $categoryTrail->prepend($trailCategory);
        $trailCategory = $trailCategory->parent;
    }
    $skuCode = strtoupper($product->slug);
@endphp

@push('head')
    @php
        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'description' => strip_tags($product->short_description ?: $product->name),
            'sku' => $product->slug,
            'offers' => [
                '@type' => 'Offer',
                'priceCurrency' => 'IRR',
                'price' => $product->sellablePrice(),
                'availability' => $product->is_active ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'url' => route('products.show', $product->slug),
            ],
        ];
        if ($mainImage) {
            $jsonLd['image'] = $mainImage;
        }
    @endphp
    <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

@section('content')
    <div
        class="fs-product-page"
        data-product-variants='@json($presenter->variantsForClient())'
        @if ($presenter->hasConfigurableVariants()) data-variant-options @endif
    >
        <nav class="fs-product-breadcrumb wrap" aria-label="مسیر">
            <a href="{{ route('home') }}">خانه</a>
            @foreach ($categoryTrail as $cat)
                <span aria-hidden="true">›</span>
                <a href="{{ route('categories.show', $cat->slug) }}">{{ $cat->name }}</a>
            @endforeach
            @if ($categoryTrail->isEmpty())
                <span aria-hidden="true">›</span>
                <a href="{{ route('products.index') }}">محصولات</a>
            @endif
            <span aria-hidden="true">›</span>
            <span>{{ $product->name }}</span>
        </nav>

        <section class="fs-product-hero wrap">
            <div class="fs-product-hero-shell">
            <div class="fs-product-gallery" data-product-gallery>
                <div class="fs-product-gallery-main">
                    @if ($mainImage)
                        <img src="{{ $mainImage }}" alt="{{ $product->name }}" data-product-main-img width="520" height="520">
                    @else
                        <span class="fs-product-gallery-placeholder">📦</span>
                    @endif
                    <div class="fs-product-gallery-actions">
                        <button type="button" class="fs-product-icon-btn" data-product-zoom aria-label="بزرگنمایی تصویر">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3M11 8v6M8 11h6"/></svg>
                        </button>
                        <button type="button" class="fs-product-icon-btn" data-product-compare="{{ $product->id }}" aria-label="افزودن به مقایسه">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M16 3h5v5M4 20 21 3M21 16v5h-5M15 15l6 6M4 4l5 5"/></svg>
                        </button>
                    </div>
                </div>
                @if (count($gallery) > 1)
                    <div class="fs-product-gallery-thumbs">
                        @foreach ($gallery as $index => $url)
                            <button
                                type="button"
                                class="fs-product-thumb"
                                data-product-thumb="{{ $url }}"
                                @if ($index === 0) data-active aria-current="true" @endif
                            >
                                <img src="{{ $url }}" alt="" width="72" height="72" loading="lazy">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="fs-product-info">
                <div class="fs-product-pills">
                    @if ($product->brand)
                        <a href="{{ route('products.index', ['brand' => $product->brand->slug]) }}" class="fs-product-pill">{{ $product->brand->name }}</a>
                    @endif
                    @if ($product->category?->parent)
                        <a href="{{ route('categories.show', $product->category->parent->slug) }}" class="fs-product-pill">{{ $product->category->parent->name }}</a>
                    @endif
                    @if ($product->category)
                        <a href="{{ route('categories.show', $product->category->slug) }}" class="fs-product-pill">{{ $product->category->name }}</a>
                    @endif
                </div>

                <h1 class="fs-product-title">{{ $product->name }}</h1>
                <p class="fs-product-sku">{{ $skuCode }}</p>

                <div class="fs-product-meta">
                    <span class="fs-product-meta-item">
                        @php
                            $avgRating = (float) $reviewAverage;
                            $filledStars = (int) min(5, round($avgRating));
                        @endphp
                        <span class="fs-product-rating-stars" role="img" aria-label="امتیاز {{ JalaliDate::toPersianDigits((string) $reviewAverage) }} از ۵">
                            @for ($star = 1; $star <= 5; $star++)
                                <span @class(['fs-product-rating-star', 'is-filled' => $star <= $filledStars]) aria-hidden="true">★</span>
                            @endfor
                        </span>
                        <span class="fs-product-rating-score">{{ JalaliDate::toPersianDigits(number_format($avgRating, 1)) }}</span>
                        <span class="fs-product-meta-muted">({{ JalaliDate::toPersianDigits((string) $reviewCount) }} دیدگاه)</span>
                    </span>
                    <a href="#product-qa" class="fs-product-meta-link">{{ JalaliDate::toPersianDigits((string) $questionCount) }} پرسش و پاسخ</a>
                </div>

                <hr class="fs-product-divider" />

                @if ($colors !== [])
                    <div class="fs-product-variants">
                        <p class="fs-product-variant-label">رنگ: <span data-color-label>{{ $colors[0]['label'] }}</span></p>
                        <div class="fs-product-swatches">
                            @foreach ($colors as $i => $swatch)
                                <button
                                    type="button"
                                    class="fs-product-swatch"
                                    style="--swatch: {{ $swatch['color'] ?? '#ccc' }}"
                                    data-swatch
                                    data-option-key="color"
                                    data-option-value="{{ $swatch['value'] }}"
                                    data-name="{{ $swatch['label'] }}"
                                    @if ($i === 0) data-active @endif
                                    aria-label="{{ $swatch['label'] }}"
                                ></button>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($optionPills !== [])
                    <div class="fs-product-variants">
                        <p class="fs-product-variant-label">{{ $optionGroupLabel }}</p>
                        <div class="fs-product-options" data-options>
                            @foreach ($optionPills as $i => $pill)
                                <button
                                    type="button"
                                    class="fs-product-option"
                                    data-option-key="{{ $presenter->primaryOptionKey() ?? 'storage' }}"
                                    data-option-value="{{ $pill['value'] }}"
                                    @if ($i === 0) data-active @endif
                                >{{ $pill['label'] }}</button>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <aside class="fs-product-buyaside">
                <div class="fs-product-buybox pcard">
                    <ul class="fs-product-seller">
                        <li>
                            <span class="fs-product-seller-icon" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 9h18v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9Z"/><path d="M3 9l2-5h14l2 5"/></svg>
                            </span>
                            <span>فروشنده: <strong>{{ $shopName }}</strong></span>
                        </li>
                        <li>
                            <span class="fs-product-seller-icon" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/></svg>
                            </span>
                            <span>۱۸ ماه گارانتی شرکتی</span>
                        </li>
                        <li>
                            <span class="fs-product-seller-icon" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 10h13v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-8Z"/><path d="M16 13V6a2 2 0 0 1 2-2h3v9"/></svg>
                            </span>
                            <span>ضمانت ۷ روزه بازگشت کالا</span>
                        </li>
                    </ul>

                    <livewire:shop.product-price-box :product-id="$product->id" :key="'price-'.$product->id" />

                    <div class="fs-product-buybox-actions">
                        <livewire:shop.add-to-cart :product-id="$product->id" ui="product-page" :key="'add-'.$product->id" />
                    </div>
                </div>

                <div class="fs-product-trust-row">
                    <div class="fs-product-trust-card pcard">
                        <span class="fs-product-trust-icon" aria-hidden="true">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92Z"/></svg>
                        </span>
                        <span>پشتیبانی ۲۴ ساعته</span>
                    </div>
                    <div class="fs-product-trust-card pcard">
                        <span class="fs-product-trust-icon" aria-hidden="true">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                        </span>
                        <span>پرداخت در محل</span>
                    </div>
                    <div class="fs-product-trust-card pcard">
                        <span class="fs-product-trust-icon" aria-hidden="true">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg>
                        </span>
                        <span>ضمانت اصالت کالا</span>
                    </div>
                </div>
            </aside>
            </div>
        </section>

        <nav class="fs-product-tabnav wrap" data-product-tabnav aria-label="بخش‌های محصول">
            <a href="#product-intro" class="fs-product-tab" data-active>معرفی محصول</a>
            <a href="#product-specs" class="fs-product-tab">مشخصات فنی</a>
            <a href="#product-reviews" class="fs-product-tab">دیدگاه خریداران</a>
            <a href="#product-qa" class="fs-product-tab">پرسش و پاسخ</a>
        </nav>

        <section id="product-intro" class="fs-product-section wrap scroll-mt-24">
            <header class="fs-product-section-head">
                <span class="fs-product-section-bar"></span>
                <h2>معرفی محصول</h2>
            </header>
            <div class="fs-product-intro-grid">
                <div class="fs-product-prose prose-fs">
                    @if (filled($product->description))
                        {!! $product->description !!}
                    @elseif (filled($product->short_description))
                        <p>{{ $product->short_description }}</p>
                    @else
                        <p class="fs-product-empty-muted">توضیحات این محصول در پنل مدیریت ثبت نشده است.</p>
                    @endif
                </div>
                <aside class="fs-product-intro-highlight">
                    <h3>{{ $product->name }}</h3>
                    <p>خرید مطمئن از {{ $shopName }}</p>
                </aside>
            </div>
        </section>

        <section id="product-specs" class="fs-product-section wrap scroll-mt-24">
            <header class="fs-product-section-head">
                <span class="fs-product-section-bar"></span>
                <h2>مشخصات فنی</h2>
            </header>
            <div class="fs-product-spec-grid">
                @foreach ($specGroups as $group)
                    <div class="fs-product-spec-group pcard">
                        <h3>{{ $group['title'] }}</h3>
                        <dl>
                            @foreach ($group['rows'] as $row)
                                <div>
                                    <dt>{{ $row['label'] }}</dt>
                                    <dd>{{ $row['value'] }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                @endforeach
            </div>
        </section>

        <div class="fs-product-ugc-grid wrap">
            <section id="product-reviews" class="fs-product-section fs-product-ugc-col scroll-mt-24">
                <header class="fs-product-section-head">
                    <span class="fs-product-section-bar"></span>
                    <h2>دیدگاه خریداران</h2>
                </header>
                <livewire:shop.product-reviews :product="$product" :key="'reviews-'.$product->id" />
            </section>

            <section id="product-qa" class="fs-product-section fs-product-ugc-col scroll-mt-24">
                <header class="fs-product-section-head">
                    <span class="fs-product-section-bar"></span>
                    <h2>پرسش و پاسخ</h2>
                </header>
                <livewire:shop.product-questions :product="$product" :key="'questions-'.$product->id" />
            </section>
        </div>

        @if ($related->isNotEmpty())
            <section class="fs-product-related wrap" data-slider aria-label="محصولات مشابه">
                <header class="fs-product-related-head">
                    <h2>محصولات مشابه</h2>
                    <div class="fs-product-related-arrows">
                        <button type="button" class="fs-slider-arrow" data-slider-prev aria-label="قبلی">‹</button>
                        <button type="button" class="fs-slider-arrow" data-slider-next aria-label="بعدی">›</button>
                    </div>
                </header>
                <div class="rail fs-product-related-track" data-slider-track>
                    @foreach ($related as $item)
                        <x-shop.product-card :product="$item" />
                    @endforeach
                </div>
            </section>
        @endif

        <div class="fs-product-mobile-bar lg:hidden" aria-hidden="false">
            <div>
                <span class="fs-product-mobile-price">{{ JalaliDate::toPersianDigits(number_format($product->sellablePrice())) }} تومان</span>
            </div>
            <a href="#product-intro" class="fs-product-mobile-buy">افزودن به سبد</a>
        </div>
    </div>

@endsection