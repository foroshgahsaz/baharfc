<header class="site-header">
    <div class="topbar container">
        <a class="brand" href="{{ route('home') }}" aria-label="{{ $shopWordmark ?? 'BAHARFC' }}">
            @if (! empty($shopLogoUrl))
                <img src="{{ $shopLogoUrl }}" alt="{{ $shopWordmark }}" class="brand-logo">
            @else
                {{ $shopWordmark ?? 'BAHARFC' }}
            @endif
        </a>

        <livewire:shop.search-bar />

        <nav class="user-links" aria-label="حساب کاربری">
            @guest
                <a href="{{ route('login') }}">
                    <span class="user-icon" aria-hidden="true">♙</span>
                    <span class="link-text">ورود / ثبت‌نام</span>
                </a>
            @else
                <a href="{{ route('account.index') }}">
                    <span class="user-icon" aria-hidden="true">♙</span>
                    <span class="link-text">{{ auth()->user()->name }}</span>
                </a>
            @endguest
            <livewire:shop.cart-header-badge />
        </nav>
    </div>

    <div class="nav-wrap">
        <div class="mobile-nav-backdrop" id="mobileNavBackdrop" aria-hidden="true"></div>
        <div class="container nav-row">
            <button class="mobile-menu" id="menuToggle" type="button" aria-expanded="false" aria-controls="mainNav" aria-label="باز کردن منو">☰</button>
            <nav class="main-nav" id="mainNav" aria-label="منوی اصلی">
                <div class="mobile-nav-head">
                    <span>منو</span>
                    <button type="button" class="mobile-nav-close" id="mobileNavClose" aria-label="بستن منو">×</button>
                </div>
                @if (($headerMenuItems ?? collect())->isNotEmpty())
                    @foreach ($headerMenuItems as $item)
                        <a
                            href="{{ $item->resolvedUrl() }}"
                            @class([
                                'nav-divider' => $item->show_divider_before,
                                'is-active' => $item->isCurrent(),
                            ])
                            @if ($item->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif
                        >{{ $item->label }}</a>
                    @endforeach
                @else
                    <a href="{{ route('products.index', ['category' => 'women']) }}">زنانه</a>
                    <a href="{{ route('products.index', ['category' => 'men']) }}">مردانه</a>
                @endif
            </nav>
            @if ($headerCta ?? null)
                <a
                    class="special"
                    href="{{ $headerCta->resolvedUrl() }}"
                    @if ($headerCta->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif
                >{{ $headerCta->label }}</a>
            @else
                <a class="special" href="{{ route('products.index', ['sort' => 'price_desc']) }}">پیشنهاد ویژه</a>
            @endif
        </div>
    </div>
</header>