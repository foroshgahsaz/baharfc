<?php

namespace App\Models;

use App\Support\ShopMenu;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class MenuItem extends Model
{
    protected static function booted(): void
    {
        $forget = fn (): mixed => ShopMenu::forgetCache();

        static::saved($forget);
        static::deleted($forget);
    }

    protected $fillable = [
        'menu_id',
        'parent_id',
        'label',
        'item_type',
        'category_id',
        'product_id',
        'blog_post_id',
        'page_key',
        'custom_url',
        'query_params',
        'sort_order',
        'is_active',
        'show_divider_before',
        'open_in_new_tab',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'show_divider_before' => 'boolean',
            'open_in_new_tab' => 'boolean',
            'query_params' => 'array',
        ];
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('sort_order');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function blogPost(): BelongsTo
    {
        return $this->belongsTo(BlogPost::class);
    }

    public function resolvedUrl(): string
    {
        return match ($this->item_type) {
            'category' => $this->category
                ? route('categories.show', $this->category->slug)
                : '#',
            'product' => $this->product
                ? route('products.show', $this->product->slug)
                : '#',
            'blog_post' => $this->blogPost
                ? route('blog.show', $this->blogPost->slug)
                : '#',
            'page' => $this->resolveBuiltinPageUrl(),
            'custom_url' => $this->custom_url ?: '#',
            default => '#',
        };
    }

    public function isCurrent(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        return match ($this->item_type) {
            'page' => match ($this->page_key) {
                'home' => request()->routeIs('home'),
                'products' => $this->matchesProductsIndex(),
                'products_special' => request()->routeIs('products.index')
                    && request()->query('sort') === 'price_desc',
                'blog' => request()->routeIs('blog.*'),
                'cart' => request()->routeIs('cart.*'),
                'login' => request()->routeIs('login'),
                'register' => request()->routeIs('register'),
                'account' => request()->routeIs('account.*'),
                default => false,
            },
            'category' => request()->routeIs('products.index')
                && request()->string('category')->toString() === ($this->category?->slug ?? ''),
            'product' => request()->routeIs('products.show')
                && request()->route('slug') === ($this->product?->slug ?? ''),
            'blog_post' => request()->routeIs('blog.show')
                && request()->route('slug') === ($this->blogPost?->slug ?? ''),
            default => false,
        };
    }

    protected function matchesProductsIndex(): bool
    {
        if (! request()->routeIs('products.index')) {
            return false;
        }

        $expected = $this->query_params ?? [];
        if ($expected === []) {
            return true;
        }

        foreach ($expected as $key => $value) {
            if ((string) request()->query($key, '') !== (string) $value) {
                return false;
            }
        }

        return true;
    }

    protected function resolveBuiltinPageUrl(): string
    {
        $params = $this->query_params ?? [];

        return match ($this->page_key) {
            'home' => route('home'),
            'products' => route('products.index', $params),
            'products_special' => route('products.index', ['sort' => 'price_desc']),
            'blog' => route('blog.index'),
            'cart' => route('cart.index'),
            'login' => route('login'),
            'register' => route('register'),
            'account' => route('account.index'),
            default => '#',
        };
    }

    public static function itemTypeOptions(): array
    {
        return [
            'page' => 'صفحهٔ آماده',
            'category' => 'دسته‌بندی',
            'product' => 'محصول',
            'blog_post' => 'مقالهٔ بلاگ',
            'custom_url' => 'لینک دلخواه',
        ];
    }

    public static function pageKeyOptions(): array
    {
        return [
            'home' => 'صفحه اصلی',
            'products' => 'فهرست محصولات',
            'products_special' => 'پیشنهاد ویژه (مرتب‌سازی قیمت)',
            'blog' => 'بلاگ',
            'cart' => 'سبد خرید',
            'login' => 'ورود',
            'register' => 'ثبت‌نام',
            'account' => 'حساب کاربری',
        ];
    }
}