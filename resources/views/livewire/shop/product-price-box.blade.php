@php
    use App\Support\JalaliDate;
@endphp

<div class="fs-product-price-block">
    <span class="fs-product-price-label">قیمت</span>
    <div class="fs-product-price">
        @if ($hasDiscount && $discountPercent)
            <span class="fs-product-price-old">{{ JalaliDate::toPersianDigits(number_format($compareAt)) }}</span>
            <span class="fs-product-discount-pill">٪{{ JalaliDate::toPersianDigits($discountPercent) }}</span>
        @endif
        <span class="fs-product-price-final">{{ JalaliDate::toPersianDigits(number_format($price)) }}</span>
        <span class="fs-product-toman">تومان</span>
    </div>
</div>