<?php

use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\ProductController;
use App\Livewire\Auth\CustomerLogin;
use App\Livewire\Auth\CustomerRegister;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', CustomerLogin::class)->name('login');
    Route::get('/register', CustomerRegister::class)->name('register');
});

Route::post('/logout', LogoutController::class)->middleware('auth')->name('logout');
