@props(['product'])

@php
    use App\Support\JalaliDate;

    $price = $product->sellablePrice();
    $hasDiscount = $product->hasDiscount();
    $percent = $product->discountPercent();
@endphp

<article class="fs-catalog-card">
    <a href="{{ route('products.show', $product->slug) }}" class="fs-catalog-card-media">
        @if ($hasDiscount && $percent)
            <span class="fs-catalog-badge">٪{{ JalaliDate::toPersianDigits((string) $percent) }}</span>
        @endif
        @if ($product->imageUrl())
            <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" loading="lazy" decoding="async" width="320" height="400" />
        @else
            <span class="fs-catalog-placeholder">📦</span>
        @endif
    </a>
    <h2 class="fs-catalog-card-title">
        <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
    </h2>
    <div class="fs-catalog-card-price">
        @if ($hasDiscount && $product->compare_at_price)
            <span class="fs-catalog-price-old">{{ JalaliDate::toPersianDigits(number_format($product->compare_at_price)) }}</span>
        @endif
        <span class="fs-catalog-price-final">{{ JalaliDate::toPersianDigits(number_format($price)) }} تومان</span>
    </div>
</article>