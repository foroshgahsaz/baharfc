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
    $shopName = ShopAppearance::siteName();
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
    <div class="fs-product-page">
        <nav class="fs-product-breadcrumb wrap" aria-label="مسیر">
            <a href="{{ route('home') }}">خانه</a>
            <span aria-hidden="true">›</span>
            <a href="{{ route('products.index') }}">محصولات</a>
            @if ($product->category)
                <span aria-hidden="true">›</span>
                <a href="{{ route('products.index', ['category' => $product->category->slug]) }}">{{ $product->category->name }}</a>
            @endif
            <span aria-hidden="true">›</span>
            <span>{{ $product->name }}</span>
        </nav>

        <section class="fs-product-hero wrap">
            <div class="fs-product-gallery pcard" data-product-gallery>
                <div class="fs-product-gallery-main">
                    @if ($mainImage)
                        <img src="{{ $mainImage }}" alt="{{ $product->name }}" data-product-main-img width="520" height="520">
                    @else
                        <span class="fs-product-gallery-placeholder">📦</span>
                    @endif
                    <div class="fs-product-gallery-actions">
                        <livewire:shop.product-wishlist-toggle :product-id="$product->id" :key="'wish-gallery-'.$product->id" />
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
                    @if ($product->category)
                        <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="fs-product-pill">{{ $product->category->name }}</a>
                    @endif
                </div>

                <h1 class="fs-product-title">{{ $product->name }}</h1>

                <div class="fs-product-meta">
                    <span class="fs-product-rating">
                        <span class="fs-product-star">★</span>
                        <span>۰</span>
                    </span>
                    <a href="#product-qa" class="fs-product-meta-link">۰ پرسش و پاسخ</a>
                    <a href="#product-reviews" class="fs-product-meta-link">۰ دیدگاه</a>
                </div>

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
                        <p class="fs-product-variant-label">گزینه</p>
                        <div class="fs-product-options" data-options>
                            @foreach ($optionPills as $i => $pill)
                                <button type="button" class="fs-product-option" @if ($i === 0) data-active @endif>{{ $pill['label'] }}</button>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($keySpecs !== [])
                    <dl class="fs-product-keyspecs">
                        @foreach ($keySpecs as $row)
                            <div>
                                <dt>{{ $row['label'] }}</dt>
                                <dd>{{ $row['value'] }}</dd>
                            </div>
                        @endforeach
                    </dl>
                @endif
            </div>

            <aside class="fs-product-buyaside">
                <div class="fs-product-buybox pcard">
                    <ul class="fs-product-seller">
                        <li>
                            <span class="fs-product-seller-icon">🏪</span>
                            <span>فروشنده: <strong>{{ $shopName }}</strong></span>
                        </li>
                        <li>
                            <span class="fs-product-seller-icon">🛡️</span>
                            <span>۱۸ ماه گارانتی شرکتی</span>
                        </li>
                        <li>
                            <span class="fs-product-seller-icon">↩️</span>
                            <span>ضمانت ۷ روزه بازگشت کالا</span>
                        </li>
                    </ul>

                    <div class="fs-product-price-block">
                        <span class="fs-product-price-label">قیمت</span>
                        <div class="fs-product-price">
                            @if ($product->hasDiscount())
                                <span class="fs-product-price-old">{{ JalaliDate::toPersianDigits(number_format($product->compare_at_price)) }}</span>
                                <span class="fs-product-discount-pill">٪{{ JalaliDate::toPersianDigits($product->discountPercent()) }}</span>
                            @endif
                            <span class="fs-product-price-final">{{ JalaliDate::toPersianDigits(number_format($product->sellablePrice())) }}</span>
                            <span class="fs-product-toman">تومان</span>
                        </div>
                    </div>

                    <div class="fs-product-buybox-actions">
                        <livewire:shop.add-to-cart :product-id="$product->id" :key="'add-'.$product->id" />
                    </div>

                    <div class="fs-product-trust">
                        <div><span>۲۴</span><small>پشتیبانی</small></div>
                        <div><span>💳</span><small>پرداخت در محل</small></div>
                        <div><span>✓</span><small>ضمانت اصالت</small></div>
                    </div>
                </div>
            </aside>
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
                    @if ($product->description)
                        {!! $product->description !!}
                    @elseif ($product->short_description)
                        <p>{{ $product->short_description }}</p>
                    @else
                        <p>نگاهی دقیق‌تر به {{ $product->name }}</p>
                        <p>این کالا با گارانتی معتبر و ضمانت اصالت ارائه می‌شود. تمام مشخصات فنی در بخش مشخصات همین صفحه آمده است.</p>
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

        <section id="product-reviews" class="fs-product-section wrap scroll-mt-24">
            <header class="fs-product-section-head">
                <span class="fs-product-section-bar"></span>
                <h2>دیدگاه خریداران</h2>
            </header>
            <div class="fs-product-empty pcard">
                <p><strong>۰</strong> دیدگاه ثبت شده</p>
                <p class="fs-product-empty-muted">هنوز دیدگاهی ثبت نشده است.</p>
                <button type="button" class="fs-product-outline-btn" disabled>ثبت دیدگاه</button>
            </div>
        </section>

        <section id="product-qa" class="fs-product-section wrap scroll-mt-24">
            <header class="fs-product-section-head">
                <span class="fs-product-section-bar"></span>
                <h2>پرسش و پاسخ</h2>
            </header>
            <div class="fs-product-empty pcard">
                <p class="fs-product-empty-muted">هنوز پرسشی ثبت نشده است.</p>
                <button type="button" class="fs-product-outline-btn" disabled>ثبت پرسش</button>
            </div>
        </section>

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