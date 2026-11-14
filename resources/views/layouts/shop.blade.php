<!DOCTYPE html>
<html lang="fa" dir="rtl" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'فروشگاه' }} — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-full bg-slate-50 font-sans text-slate-800 antialiased">
    <header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/90 backdrop-blur-md">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="text-lg font-bold tracking-tight text-brand-700">
                {{ config('app.name') }}
            </a>
            <div class="hidden flex-1 justify-center px-4 md:flex">
                <livewire:shop.search-bar />
            </div>
            <nav class="hidden items-center gap-6 text-sm font-medium text-slate-600 md:flex">
                <a href="{{ route('home') }}" class="transition hover:text-brand-700">خانه</a>
                <a href="{{ route('products.index') }}" class="transition hover:text-brand-700">محصولات</a>
                <a href="#" class="transition hover:text-brand-700">تماس</a>
            </nav>
            <div class="flex items-center gap-2">
                @auth
                    <span class="hidden text-sm text-slate-600 sm:inline">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-xl border border-slate-200 px-3 py-1.5 text-sm transition hover:border-brand-300 hover:text-brand-700">
                            خروج
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded-xl bg-brand-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-brand-700">
                        ورود / ثبت‌نام
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <main>
        @hasSection('content')
            @yield('content')
        @else
            {{ $slot ?? '' }}
        @endif
    </main>

    <footer class="mt-16 border-t border-slate-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-10 text-center text-sm text-slate-500 sm:px-6 lg:px-8">
            <p>فروشگاه اینترنتی {{ config('app.name') }} — طراحی اولیه (قابل جایگزینی با UI اختصاصی شما)</p>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
