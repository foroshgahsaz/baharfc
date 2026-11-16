<!DOCTYPE html>
<html lang="fa" dir="rtl" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'فروشگاه' }} — {{ $shopSiteName ?? config('app.name') }}</title>
    @if (! empty($shopAccent))
        <style>:root { --lebasino-accent: {{ $shopAccent }}; }</style>
    @endif
    @if (! empty($shopMetaDescription))
        <meta name="description" content="{{ $shopMetaDescription }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('head')
</head>
<body class="lebasino-page min-h-full bg-white font-sans text-slate-800 antialiased">
    @include('components.shop.site-header')

    <main @if (! empty($mainClass)) class="{{ $mainClass }}" @endif>
        @hasSection('content')
            @yield('content')
        @else
            {{ $slot ?? '' }}
        @endif
    </main>

    <livewire:shop.cart-drawer />

    <footer class="shop-footer">
        <div class="container shop-footer-inner">
            <p>{{ $shopFooterText ?? 'فروشگاه اینترنتی '.($shopSiteName ?? config('app.name')) }}</p>
        </div>
    </footer>

    @livewireScripts
    @stack('scripts')
</body>
</html>