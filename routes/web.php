<?php

use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Payment\PaymentCallbackController;
use App\Http\Controllers\Shop\BlogController;
use App\Http\Controllers\Shop\BlogFeedController;
use App\Http\Controllers\Shop\ContactController;
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\OrderInvoiceController;
use App\Http\Controllers\Shop\ProductController;
use App\Http\Controllers\Shop\SitemapController;
use App\Livewire\Account\AccountIndex;
use App\Livewire\Auth\CustomerLogin;
use App\Livewire\Auth\CustomerRegister;
use App\Livewire\Shop\CartPage;
use App\Livewire\Shop\CheckoutPage;
use App\Livewire\Shop\CheckoutPayment;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/contact', ContactController::class)->name('contact');

Route::get('/blog/feed.xml', BlogFeedController::class)->name('blog.feed');
Route::get('/blog/search', [BlogController::class, 'search'])->name('blog.search');
Route::get('/blog/category/{slug}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/blog/tag/{slug}', [BlogController::class, 'tag'])->name('blog.tag');
Route::get('/blog/author/{author}', [BlogController::class, 'author'])->name('blog.author');
Route::get('/blog/archive/{year}/{month?}', [BlogController::class, 'archive'])->name('blog.archive');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/category/{slug}', [ProductController::class, 'category'])->name('categories.show');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/cart', CartPage::class)->name('cart.index');

Route::middleware(['auth', 'throttle:checkout'])->group(function (): void {
    Route::get('/checkout', CheckoutPage::class)->name('checkout.index');
    Route::get('/checkout/payment/{order}', CheckoutPayment::class)->name('checkout.payment');
});

Route::match(['get', 'post'], '/payment/callback/{gateway}', PaymentCallbackController::class)
    ->middleware('throttle:payment-callbacks')
    ->name('payment.callback');

Route::middleware(['guest', 'throttle:auth-pages'])->group(function (): void {
    Route::get('/login', CustomerLogin::class)->name('login');
    Route::get('/register', CustomerRegister::class)->name('register');
});

Route::post('/logout', LogoutController::class)->middleware('auth')->name('logout');

Route::middleware('auth')->prefix('account')->name('account.')->group(function (): void {
    Route::get('/', AccountIndex::class)->name('index');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/orders/{order}/invoice', OrderInvoiceController::class)->name('orders.invoice');
});
