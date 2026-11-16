<header class="site-header">
    <div class="topbar container">
        <a class="brand" href="{{ route('home') }}" aria-label="{{ config('shop.logo_wordmark', 'LEBASINO') }}">
            {{ config('shop.logo_wordmark', 'LEBASINO') }}
        </a>

        <livewire:shop.search-bar />

        <nav class="user-links" aria-label="حساب کاربری">
            <a href="{{ auth()->check() ? route('account.index') : route('login') }}">
                <span class="heart" aria-hidden="true">♡</span>
                <span class="link-text">لیست علاقه‌مندی</span>
                <b>{{ $wishlistCount ?? 0 }}</b>
            </a>
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
            <a href="{{ route('cart.index') }}">
                <span class="bag" aria-hidden="true">♧</span>
                <span class="link-text">{{ number_format($cartSubtotal ?? 0) }} تومان</span>
                <b>{{ $cartItemCount ?? 0 }}</b>
            </a>
        </nav>
    </div>

    <div class="nav-wrap">
        <div class="container nav-row">
            <button class="mobile-menu" id="menuToggle" type="button" aria-expanded="false" aria-controls="mainNav">☰</button>
            <nav class="main-nav" id="mainNav">
                @forelse ($headerGenderCategories ?? [] as $gender)
                    <a href="{{ route('products.index', ['category' => $gender->slug]) }}">{{ $gender->name }}</a>
                @empty
                    <a href="{{ route('products.index', ['category' => 'women']) }}">زنانه</a>
                    <a href="{{ route('products.index', ['category' => 'men']) }}">مردانه</a>
                @endforelse
                @foreach (config('shop.nav_links', []) as $link)
                    @php
                        $isActive = isset($link['active']) && request()->routeIs($link['active']);
                        $href = isset($link['params'])
                            ? route($link['route'], $link['params'])
                            : route($link['route']);
                        $isHome = ($link['label'] ?? '') === 'صفحه اصلی';
                    @endphp
                    <a
                        href="{{ $href }}"
                        @class(['nav-divider' => $isHome, 'is-active' => $isActive])
                    >{{ $link['label'] }}</a>
                @endforeach
            </nav>
            <a class="special" href="{{ route('products.index', ['sort' => 'price_desc']) }}">پیشنهاد ویژه</a>
        </div>
    </div>
</header>