<?php

namespace App\Support\AdminBar;

use App\Filament\Pages\MediaLibrary;
use App\Filament\Pages\ShopSettings;
use App\Filament\Resources\BlogPosts\BlogPostResource;
use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\Products\ProductResource;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Support\ShopAppearance;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class AdminBarBuilder
{
    public function forShop(Request $request): ?AdminBarPresenter
    {
        $user = $request->user();

        if (! $this->shouldShow($user)) {
            return null;
        }

        $links = [
            new AdminBarItem('داشبورد', $this->panelHomeUrl()),
        ];

        $routeName = $request->route()?->getName();

        match ($routeName) {
            'products.show' => $this->appendProductShopLinks(
                $links,
                Product::query()->where('slug', (string) $request->route('slug'))->first(),
                $user,
            ),
            'blog.show' => $this->appendBlogShopLinks(
                $links,
                BlogPost::query()->where('slug', (string) $request->route('slug'))->first(),
                $user,
            ),
            'products.index' => $this->appendProductsIndexLinks($links, $request, $user),
            'contact' => $this->appendContactLinks($links, $user),
            'home' => $this->appendHomeLinks($links, $user),
            default => $this->appendDefaultShopLinks($links, $user),
        };

        return new AdminBarPresenter(
            user: $user,
            siteName: ShopAppearance::siteName(),
            panelHomeUrl: $this->panelHomeUrl(),
            shopHomeUrl: route('home'),
            logoutUrl: $this->logoutUrl(),
            links: $links,
            context: 'shop',
        );
    }

    public function forFilament(Request $request): ?AdminBarPresenter
    {
        $user = $request->user();

        if (! $this->shouldShow($user)) {
            return null;
        }

        $links = [
            new AdminBarItem('مشاهده فروشگاه', route('home'), openInNewTab: true),
            new AdminBarItem('داشبورد', $this->panelHomeUrl()),
        ];

        $routeName = $request->route()?->getName() ?? '';

        if (str_starts_with($routeName, 'filament.admin.resources.products.')) {
            $this->appendProductPanelLinks($links, $request, $user);
        } elseif (str_starts_with($routeName, 'filament.admin.resources.blog-posts.')) {
            $this->appendBlogPanelLinks($links, $request, $user);
        } elseif (str_starts_with($routeName, 'filament.admin.resources.categories.')) {
            $this->appendCategoryPanelLinks($links, $request, $user);
        } elseif (str_starts_with($routeName, 'filament.admin.resources.orders.')) {
            $this->appendOrderPanelLinks($links, $request, $user);
        } elseif ($routeName === 'filament.admin.pages.shop-settings') {
            $links[] = new AdminBarItem('مشاهده صفحه اصلی سایت', route('home'), openInNewTab: true);
        } elseif ($routeName === 'filament.admin.resources.products.index') {
            $links[] = new AdminBarItem('مشاهده لیست در سایت', route('products.index'), openInNewTab: true);
        } elseif ($routeName === 'filament.admin.resources.blog-posts.index') {
            $links[] = new AdminBarItem('مشاهده بلاگ در سایت', route('blog.index'), openInNewTab: true);
        }

        $this->appendDefaultPanelLinks($links, $user);

        return new AdminBarPresenter(
            user: $user,
            siteName: ShopAppearance::siteName(),
            panelHomeUrl: $this->panelHomeUrl(),
            shopHomeUrl: route('home'),
            logoutUrl: Filament::getLogoutUrl(),
            links: $links,
            context: 'filament',
        );
    }

    private function shouldShow(?User $user): bool
    {
        return $user instanceof User
            && $user->hasAdminPanelAccess()
            && $user->canAdmin('panel.access');
    }

    /**
     * @param  list<AdminBarItem>  $links
     */
    private function appendProductShopLinks(array &$links, mixed $product, User $user): void
    {
        if (! $product instanceof Product) {
            return;
        }

        if ($user->canAdmin('products.update') && ProductResource::canEdit($product)) {
            $links[] = new AdminBarItem(
                'ویرایش محصول',
                ProductResource::getUrl('edit', ['record' => $product]),
                highlight: true,
            );
        }

        if ($user->canAdmin('products.view')) {
            $links[] = new AdminBarItem('همهٔ محصولات', ProductResource::getUrl('index'));
        }
    }

    /**
     * @param  list<AdminBarItem>  $links
     */
    private function appendBlogShopLinks(array &$links, mixed $post, User $user): void
    {
        if (! $post instanceof BlogPost) {
            return;
        }

        if ($user->canAdmin('blog_posts.update') && BlogPostResource::canEdit($post)) {
            $links[] = new AdminBarItem(
                'ویرایش نوشته',
                BlogPostResource::getUrl('edit', ['record' => $post]),
                highlight: true,
            );
        }

        if ($user->canAdmin('blog_posts.view')) {
            $links[] = new AdminBarItem('همهٔ نوشته‌ها', BlogPostResource::getUrl('index'));
        }
    }

    /**
     * @param  list<AdminBarItem>  $links
     */
    private function appendProductsIndexLinks(array &$links, Request $request, User $user): void
    {
        $categorySlug = $request->string('category')->trim()->toString();
        $category = $categorySlug !== ''
            ? Category::query()->where('slug', $categorySlug)->first()
            : null;

        if ($category instanceof Category && $user->canAdmin('categories.update') && CategoryResource::canEdit($category)) {
            $links[] = new AdminBarItem(
                'ویرایش دسته',
                CategoryResource::getUrl('edit', ['record' => $category]),
                highlight: true,
            );
        }

        if ($user->canAdmin('products.create')) {
            $links[] = new AdminBarItem('افزودن محصول', ProductResource::getUrl('create'));
        }
    }

    /**
     * @param  list<AdminBarItem>  $links
     */
    private function appendContactLinks(array &$links, User $user): void
    {
        if ($user->canAdmin('settings.shop')) {
            $links[] = new AdminBarItem('تنظیمات فرم تماس', ShopSettings::getUrl(), highlight: true);
        }
    }

    /**
     * @param  list<AdminBarItem>  $links
     */
    private function appendHomeLinks(array &$links, User $user): void
    {
        if ($user->canAdmin('settings.shop')) {
            $links[] = new AdminBarItem('تنظیمات صفحه اصلی', ShopSettings::getUrl());
        }
    }

    /**
     * @param  list<AdminBarItem>  $links
     */
    private function appendDefaultShopLinks(array &$links, User $user): void
    {
        if ($user->canAdmin('products.view')) {
            $links[] = new AdminBarItem('محصولات', ProductResource::getUrl('index'));
        }

        if ($user->canAdmin('blog_posts.view')) {
            $links[] = new AdminBarItem('بلاگ', BlogPostResource::getUrl('index'));
        }

        if ($user->canAdmin('media.view')) {
            $links[] = new AdminBarItem('رسانه', MediaLibrary::getUrl());
        }
    }

    /**
     * @param  list<AdminBarItem>  $links
     */
    private function appendProductPanelLinks(array &$links, Request $request, User $user): void
    {
        $product = $this->resolveRecord($request, Product::class);

        if (! $product instanceof Product) {
            return;
        }

        if ($user->canAdmin('products.view')) {
            $links[] = new AdminBarItem(
                'مشاهده در سایت',
                route('products.show', $product->slug),
                highlight: true,
                openInNewTab: true,
            );
        }

        if ($request->routeIs('filament.admin.resources.products.edit') === false
            && $user->canAdmin('products.update')
            && ProductResource::canEdit($product)) {
            $links[] = new AdminBarItem(
                'ویرایش محصول',
                ProductResource::getUrl('edit', ['record' => $product]),
            );
        }
    }

    /**
     * @param  list<AdminBarItem>  $links
     */
    private function appendBlogPanelLinks(array &$links, Request $request, User $user): void
    {
        $post = $this->resolveRecord($request, BlogPost::class);

        if (! $post instanceof BlogPost) {
            return;
        }

        if ($user->canAdmin('blog_posts.view')) {
            $links[] = new AdminBarItem(
                'مشاهده در سایت',
                route('blog.show', $post->slug),
                highlight: true,
                openInNewTab: true,
            );
        }

        if ($request->routeIs('filament.admin.resources.blog-posts.edit') === false
            && $user->canAdmin('blog_posts.update')
            && BlogPostResource::canEdit($post)) {
            $links[] = new AdminBarItem(
                'ویرایش نوشته',
                BlogPostResource::getUrl('edit', ['record' => $post]),
            );
        }
    }

    /**
     * @param  list<AdminBarItem>  $links
     */
    private function appendCategoryPanelLinks(array &$links, Request $request, User $user): void
    {
        $category = $this->resolveRecord($request, Category::class);

        if (! $category instanceof Category) {
            return;
        }

        if ($user->canAdmin('categories.view')) {
            $links[] = new AdminBarItem(
                'مشاهده دسته در فروشگاه',
                route('products.index', ['category' => $category->slug]),
                highlight: true,
                openInNewTab: true,
            );
        }
    }

    /**
     * @param  list<AdminBarItem>  $links
     */
    private function appendOrderPanelLinks(array &$links, Request $request, User $user): void
    {
        $order = $this->resolveRecord($request, Order::class);

        if (! $order instanceof Order) {
            return;
        }

        if (Route::has('orders.invoice') && $user->canAdmin('orders.view')) {
            $links[] = new AdminBarItem(
                'فاکتور سفارش',
                route('orders.invoice', $order),
                highlight: true,
                openInNewTab: true,
            );
        }
    }

    /**
     * @param  list<AdminBarItem>  $links
     */
    private function appendDefaultPanelLinks(array &$links, User $user): void
    {
        if ($user->canAdmin('products.view')) {
            $links[] = new AdminBarItem('محصولات', ProductResource::getUrl('index'));
        }

        if ($user->canAdmin('settings.shop')) {
            $links[] = new AdminBarItem('تنظیمات سایت', ShopSettings::getUrl());
        }
    }

    /**
     * @template T of Model
     *
     * @param  class-string<T>  $modelClass
     * @return T|null
     */
    private function resolveRecord(Request $request, string $modelClass): ?Model
    {
        $record = $request->route('record');

        if ($record instanceof $modelClass) {
            return $record;
        }

        if (is_numeric($record)) {
            return $modelClass::query()->find((int) $record);
        }

        return null;
    }

    private function panelHomeUrl(): string
    {
        return Filament::getUrl();
    }

    private function logoutUrl(): string
    {
        if (Route::has('filament.admin.auth.logout')) {
            return route('filament.admin.auth.logout');
        }

        return route('logout');
    }
}