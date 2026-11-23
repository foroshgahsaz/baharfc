<?php

namespace App\Http\Controllers\Shop;

use App\Enums\ProductType;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Schema;
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

        $banners = Banner::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $specialDealProducts = Product::query()
            ->where('is_active', true)
            ->where('type', ProductType::Simple)
            ->whereNotNull('compare_at_price')
            ->whereColumn('compare_at_price', '>', 'price')
            ->orderByRaw('((compare_at_price - price) / compare_at_price) DESC')
            ->orderBy('sort_order')
            ->limit(12)
            ->get();

        $homeCategories = collect();
        if (Schema::hasColumn('categories', 'show_on_home')) {
            $homeCategories = Category::query()
                ->where('is_active', true)
                ->where('show_on_home', true)
                ->when(
                    Schema::hasTable('products'),
                    fn ($query) => $query->withCount(['products' => fn ($products) => $products->where('is_active', true)]),
                )
                ->orderBy('sort_order')
                ->limit(24)
                ->get();
        }

        return view('shop.home', [
            'banners' => $banners,
            'homeCategories' => $homeCategories,
            'featuredProducts' => $featuredProducts,
            'latestProducts' => $latestProducts,
            'specialDealProducts' => $specialDealProducts,
            'mainClass' => 'container',
        ]);
    }
}
