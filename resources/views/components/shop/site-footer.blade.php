@php
    use App\Support\JalaliDate;
    use App\Support\ShopAppearance;

    $shopName = ShopAppearance::siteName();
    $year = JalaliDate::toPersianDigits((string) now()->year);
@endphp

<footer class="fs-site-footer shop-footer">
    <div class="wrap fs-site-footer-grid">
        <div class="fs-site-footer-col fs-site-footer-about">
            <h3>{{ $shopName }}</h3>
            <p>{{ $shopFooterText ?? 'خرید آنلاین با ضمانت اصالت کالا، پشتیبانی و ارسال سریع.' }}</p>
        </div>
        <div class="fs-site-footer-col">
            <h4>دسترسی سریع</h4>
            <ul>
                <li><a href="{{ route('home') }}">صفحه اصلی</a></li>
                <li><a href="{{ route('products.index') }}">فروشگاه</a></li>
                @if ($shopContactEnabled ?? false)
                    <li><a href="{{ route('contact') }}">تماس با ما</a></li>
                @endif
            </ul>
        </div>
        <div class="fs-site-footer-col">
            <h4>خدمات مشتریان</h4>
            <ul>
                <li>پشتیبانی ۲۴ ساعته</li>
                <li>ضمانت اصالت کالا</li>
                <li>امکان مرجوعی ۷ روزه</li>
            </ul>
        </div>
        <div class="fs-site-footer-col fs-site-footer-trust">
            <div class="fs-site-footer-badge">پشتیبانی</div>
            <div class="fs-site-footer-badge">اصالت</div>
            <div class="fs-site-footer-badge">ارسال</div>
        </div>
    </div>
    <div class="fs-site-footer-bottom wrap">
        <p>© {{ $year }} {{ $shopName }} — تمامی حقوق محفوظ است.</p>
    </div>
</footer>