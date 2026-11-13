<?php

use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\ProductController;
use App\Livewire\Account\AccountIndex;
use App\Livewire\Auth\CustomerLogin;
use App\Livewire\Auth\CustomerRegister;
use App\Livewire\Shop\CartPage;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/cart', CartPage::class)->name('cart.index');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', CustomerLogin::class)->name('login');
    Route::get('/register', CustomerRegister::class)->name('register');
});

Route::post('/logout', LogoutController::class)->middleware('auth')->name('logout');

Route::middleware('auth')->prefix('account')->name('account.')->group(function (): void {
    Route::get('/', AccountIndex::class)->name('index');
});
