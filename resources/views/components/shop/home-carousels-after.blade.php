@props(['anchor', 'grouped' => []])

@php
    $carousels = $grouped[$anchor] ?? collect();
@endphp

@foreach ($carousels as $section)
    @php
        $products = $section->resolvedProducts();
    @endphp
    @if ($products->isNotEmpty())
        @if ($section->isStyle2())
            <x-shop.home-carousel-style-2 :section="$section" :products="$products" />
        @else
            <x-shop.home-carousel-style-1 :section="$section" :products="$products" />
        @endif
    @endif
@endforeach