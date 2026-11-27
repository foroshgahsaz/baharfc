<header class="site-header">
    <div class="topbar container">
        <div class="topbar-start">
            <button class="mobile-menu" id="menuToggle" type="button" aria-expanded="false" aria-controls="mainNav" aria-label="باز کردن منو">☰</button>
            <a class="brand" href="{{ route('home') }}" aria-label="{{ $shopWordmark ?? 'BAHARFC' }}">
                @if (! empty($shopLogoUrl))
                    <img src="{{ $shopLogoUrl }}" alt="{{ $shopWordmark }}" class="brand-logo">
                @else
                    {{ $shopWordmark ?? 'BAHARFC' }}
                @endif
            </a>
        </div>

        <div @class([
            'topbar-search',
            'topbar-search--mobile-icon' => $mobileSearchIconEnabled ?? true,
        ]) id="shopSearch">
            @if ($mobileSearchIconEnabled ?? true)
                <button type="button" class="mobile-search-trigger" id="mobileSearchOpen" aria-expanded="false" aria-controls="mobileSearchSheet" aria-label="جستجو">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8" fill="none" stroke="currentColor" stroke-width="2"/><path d="m16 16 5 5" stroke="currentColor" stroke-width="2"/></svg>
                </button>
            @endif
            <div class="topbar-search-inner" id="mobileSearchSheet">
                <div class="mobile-search-sheet-head">
                    <span>جستجوی محصولات</span>
                    <button type="button" class="mobile-search-close" id="mobileSearchClose" aria-label="بستن جستجو">×</button>
                </div>
                <livewire:shop.search-bar />
            </div>
        </div>

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
            <ul class="main-nav" id="mainNav" aria-label="منوی اصلی">
                <li class="mobile-nav-head">
                    <span>منو</span>
                    <button type="button" class="mobile-nav-close" id="mobileNavClose" aria-label="بستن منو">×</button>
                </li>
                @if (($headerMenuItems ?? collect())->isNotEmpty())
                    @foreach ($headerMenuItems as $item)
                        <x-shop.nav-menu-item :item="$item" />
                    @endforeach
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('categories.show', 'women') }}">زنانه</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('categories.show', 'men') }}">مردانه</a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</header>