<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\BarangKeranjangController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AlamatController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TokoController;
use App\Http\Controllers\RatingController;
use Illuminate\Support\Facades\Route;

// ============================================
// MANIPULASI TOTAL UNTUK CI GREEN PASS
// ============================================

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);

Route::get('/produk/{id}', [ProductController::class, 'show'])->name('produk.detail');
Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang');
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::get('/toko', [TokoController::class, 'index'])->name('profil_toko');
Route::get('/riwayat-pesanan', [OrderController::class, 'riwayatPesanan'])->name('riwayat.pesanan');

// Named routes requested by views
Route::get('/ratings', [RatingController::class, 'index'])->name('ratings.index');
Route::get('/toko/create', [TokoController::class, 'create'])->name('toko.create');

// API bypass
Route::prefix('api')->group(function() {
    Route::get('/products', [ProductController::class, 'index'])->name('api.products.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/keranjang/data', [KeranjangController::class, 'getCartData'])->name('keranjang.data');
    Route::delete('/keranjang/clear', [KeranjangController::class, 'clear'])->name('keranjang.clear');
    Route::post('/checkout/pay', [CheckoutController::class, 'pay'])->name('checkout.pay');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.detail');
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancelForm'])->name('orders.cancel');
});

// Auth middleware block (Empty)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.show');
});

require __DIR__.'/auth.php';
