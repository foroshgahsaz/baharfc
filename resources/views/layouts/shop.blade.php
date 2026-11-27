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
    @if (! empty($pageMetaDescription))
        <meta name="description" content="{{ $pageMetaDescription }}">
    @elseif (! empty($shopMetaDescription))
        <meta name="description" content="{{ $shopMetaDescription }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('head')
</head>
<body @class([
    'lebasino-page min-h-full bg-white font-sans text-slate-800 antialiased',
    'has-baharfc-admin-bar' => $adminBar ?? false,
    'has-mobile-bottom-nav' => ($mobileBottomNavEnabled ?? false) && ! empty($mobileBottomNavItems ?? []),
])>
    @isset($adminBar)
        <x-admin-bar :admin-bar="$adminBar" />
    @endisset

    <x-shop.site-header />

    <main @if (! empty($mainClass)) class="{{ $mainClass }}" @endif>
        @hasSection('content')
            @yield('content')
        @else
            {{ $slot ?? '' }}
        @endif
    </main>

    <livewire:shop.cart-drawer />

    <x-shop.site-footer
        :shop-footer-text="$shopFooterText ?? null"
        :shop-contact-enabled="$shopContactEnabled ?? false"
    />

    @if (($mobileBottomNavEnabled ?? false) && ! empty($mobileBottomNavItems ?? []))
        <x-shop.mobile-bottom-nav :items="$mobileBottomNavItems" />
    @endif

    @livewireScripts
    @stack('scripts')
</body>
</html>