<header class="z-40 border-b border-slate-100 bg-white">
    <div class="mx-auto max-w-[90rem] px-4 pt-5 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center gap-4 lg:flex-nowrap lg:gap-8">
            <a href="{{ route('home') }}" class="shop-logo shrink-0 order-1">
                {{ config('app.name') }}
            </a>

            <div class="order-3 w-full lg:order-2 lg:flex-1 lg:max-w-2xl lg:mx-auto">
                <livewire:shop.search-bar />
            </div>

            <div class="order-2 ms-auto flex items-center gap-5 text-slate-700 lg:order-3">
                <a href="{{ auth()->check() ? route('account.index') : route('login') }}" class="hidden items-center gap-2 text-sm transition hover:text-accent sm:flex">
                    <span class="relative inline-flex">
                        <svg class="size-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <span class="shop-icon-badge">{{ $wishlistCount ?? 0 }}</span>
                    </span>
                    <span class="whitespace-nowrap">لیست علاقه‌مندی‌ها</span>
                </a>

                @auth
                    <a href="{{ route('account.index') }}" class="hidden items-center gap-2 text-sm transition hover:text-accent sm:flex">
                        <svg class="size-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="max-w-[7rem] truncate">{{ auth()->user()->name }}</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hidden items-center gap-2 text-sm transition hover:text-accent sm:flex">
                        <svg class="size-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span>ورود / ثبت‌نام</span>
                    </a>
                @endauth

                <a href="{{ route('cart.index') }}" class="flex items-center gap-2 text-sm transition hover:text-accent">
                    <span class="relative inline-flex">
                        <svg class="size-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <span class="shop-icon-badge">{{ $cartItemCount ?? 0 }}</span>
                    </span>
                    <span class="whitespace-nowrap">{{ number_format($cartSubtotal ?? 0) }}<span class="text-xs text-slate-500"> تومان</span></span>
                </a>
            </div>
        </div>

        <nav class="mt-5 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 py-3 text-sm text-slate-700">
            <div class="flex items-center gap-4 font-medium">
                @forelse ($headerGenderCategories ?? [] as $gender)
                    @if (! $loop->first)
                        <span class="h-4 w-px bg-slate-200" aria-hidden="true"></span>
                    @endif
                    <a href="{{ route('products.index', ['category' => $gender->slug]) }}" class="transition hover:text-accent">{{ $gender->name }}</a>
                @empty
                    <a href="{{ route('products.index', ['category' => 'women']) }}" class="transition hover:text-accent">زنانه</a>
                    <span class="h-4 w-px bg-slate-200" aria-hidden="true"></span>
                    <a href="{{ route('products.index', ['category' => 'men']) }}" class="transition hover:text-accent">مردانه</a>
                @endforelse
            </div>

            <div class="hidden flex-wrap items-center justify-center gap-5 lg:flex">
                <a href="{{ route('home') }}" class="transition hover:text-accent">صفحه اصلی</a>
                <a href="{{ route('products.index') }}" class="transition hover:text-accent">محصولات</a>
                @foreach ($headerNavCategories ?? [] as $navCategory)
                    <a href="{{ route('products.index', ['category' => $navCategory->slug]) }}" class="transition hover:text-accent">{{ $navCategory->name }}</a>
                @endforeach
                <a href="{{ route('blog.index') }}" class="transition hover:text-accent">بلاگ</a>
            </div>

            <a
                href="{{ route('products.index', ['sort' => 'price_desc']) }}"
                class="rounded-full bg-accent-light px-4 py-1.5 text-xs font-semibold text-accent transition hover:bg-accent-muted"
            >
                پیشنهاد ویژه
            </a>
        </nav>
    </div>
</header>