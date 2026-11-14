<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::query()
            ->with(['category', 'brand'])
            ->where('is_active', true);

        if ($slug = $request->string('category')->toString()) {
            $category = Category::query()->where('slug', $slug)->where('is_active', true)->firstOrFail();
            $query->where('category_id', $category->id);
        } else {
            $category = null;
        }

        if ($brandSlug = $request->string('brand')->toString()) {
            $query->whereHas('brand', fn ($q) => $q->where('slug', $brandSlug)->where('is_active', true));
        }

        $sort = $request->string('sort', 'newest')->toString();
        match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'name' => $query->orderBy('name'),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();

        return view('shop.products.index', [
            'products' => $products,
            'category' => $category ?? null,
            'categories' => Category::query()->where('is_active', true)->whereNull('parent_id')->orderBy('sort_order')->get(),
            'brands' => Brand::query()->where('is_active', true)->orderBy('name')->get(),
            'sort' => $sort,
        ]);
    }

    public function show(string $slug): View
    {
        $product = Product::query()
            ->with(['category', 'brand', 'activeVariants'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $related = Product::query()
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->when($product->category_id, fn ($q) => $q->where('category_id', $product->category_id))
            ->limit(4)
            ->get();

        return view('shop.products.show', [
            'product' => $product,
            'related' => $related,
        ]);
    }
}
