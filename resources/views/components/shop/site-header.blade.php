<header class="bg-white pt-4">
    <div class="lebasino-shell">
        <div class="flex items-center gap-4 lg:gap-6">
            <a href="{{ route('home') }}" class="lebasino-logo shrink-0">
                {{ config('shop.logo_wordmark') }}
            </a>

            <div class="min-w-0 flex-1">
                <livewire:shop.search-bar />
            </div>

            <div class="flex shrink-0 items-start gap-1 sm:gap-3">
                <a href="{{ auth()->check() ? route('account.index') : route('login') }}" class="lebasino-action">
                    <span class="lebasino-action__icon">
                        <svg class="size-[26px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <span class="lebasino-badge">{{ $wishlistCount ?? 0 }}</span>
                    </span>
                    <span>لیست علاقه‌مندی‌ها</span>
                </a>

                @guest
                    <a href="{{ route('login') }}" class="lebasino-action hidden sm:flex">
                        <span class="lebasino-action__icon">
                            <svg class="size-[26px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </span>
                        <span>ورود / ثبت‌نام</span>
                    </a>
                @else
                    <a href="{{ route('account.index') }}" class="lebasino-action hidden sm:flex">
                        <span class="lebasino-action__icon">
                            <svg class="size-[26px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </span>
                        <span class="max-w-[4.5rem] truncate">{{ auth()->user()->name }}</span>
                    </a>
                @endguest

                <a href="{{ route('cart.index') }}" class="lebasino-action">
                    <span class="lebasino-action__icon">
                        <svg class="size-[26px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M6 6h15l-1.2 10.8H7.4L6 6zm0 0L5 3H3M9 21a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z"/>
                        </svg>
                        <span class="lebasino-badge">{{ $cartItemCount ?? 0 }}</span>
                    </span>
                    <span>{{ number_format($cartSubtotal ?? 0) }} تومان</span>
                </a>
            </div>
        </div>

        <nav class="lebasino-nav">
            <div class="flex flex-wrap items-center gap-3 lg:gap-4">
                <div class="lebasino-nav__gender">
                    @forelse ($headerGenderCategories ?? [] as $gender)
                        <a href="{{ route('products.index', ['category' => $gender->slug]) }}" class="hover:text-accent">{{ $gender->name }}</a>
                    @empty
                        <a href="{{ route('products.index', ['category' => 'women']) }}" class="hover:text-accent">زنانه</a>
                        <a href="{{ route('products.index', ['category' => 'men']) }}" class="hover:text-accent">مردانه</a>
                    @endforelse
                </div>

                <div class="lebasino-nav__links">
                    @foreach (config('shop.nav_links') as $link)
                        @php
                            $isActive = isset($link['active']) && request()->routeIs($link['active']);
                            $href = isset($link['params'])
                                ? route($link['route'], $link['params'])
                                : route($link['route']);
                        @endphp
                        <a href="{{ $href }}" @class(['lebasino-nav__link', 'lebasino-nav__link--active' => $isActive])>
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            <a href="{{ route('products.index', ['sort' => 'price_desc']) }}" class="lebasino-offer-pill">
                پیشنهاد ویژه
            </a>
        </nav>
    </div>
</header>