<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', [ProductController::class, 'home'])->name('home');

// Public routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    Route::get('/cart', [CartController::class, 'show'])->name('cart.show');
    Route::post('/cart/items', [CartController::class, 'addItem'])->name('cart.items.store');
    Route::put('/cart/items/{id}', [CartController::class, 'updateItem'])->name('cart.items.update');
    Route::delete('/cart/items/{id}', [CartController::class, 'removeItem'])->name('cart.items.destroy');
    Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');

    Route::get('/checkout', function () {
        return redirect()->route('cart.show');
    })->name('checkout.show');
    Route::post('/checkout/process', [\App\Http\Controllers\CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success', [\App\Http\Controllers\CheckoutController::class, 'success'])->name('checkout.success');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
});

require __DIR__.'/settings.php';
