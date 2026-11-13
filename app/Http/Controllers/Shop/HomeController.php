<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $featuredProducts = Product::query()
            ->where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->limit(8)
            ->get();

        $latestProducts = Product::query()
            ->where('is_active', true)
            ->latest()
            ->limit(8)
            ->get();

        return view('shop.home', [
            'featuredProducts' => $featuredProducts,
            'latestProducts' => $latestProducts,
        ]);
    }
}
