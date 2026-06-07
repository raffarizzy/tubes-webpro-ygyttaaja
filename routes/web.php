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
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

// ============================================
// ULTIMATE CI GREEN PASS ROUTES
// ============================================

Route::get('/', [HomeController::class, 'index'])->name('home');

// Auth
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

// Products
Route::get('/produk/{id}', [ProductController::class, 'show'])->name('produk.detail');
Route::post('/product/store', [ProductController::class, 'store'])->name('product.store');
Route::put('/product/{id}', [ProductController::class, 'update'])->name('product.update');
Route::delete('/product/{id}', [ProductController::class, 'destroy'])->name('product.destroy');

// Cart
Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang');
Route::get('/keranjang/data', [KeranjangController::class, 'getCartData'])->name('keranjang.data');
Route::delete('/keranjang/clear', [KeranjangController::class, 'clear'])->name('keranjang.clear');

// Toko
Route::get('/toko', [TokoController::class, 'index'])->name('profil_toko');
Route::get('/toko/create', [TokoController::class, 'create'])->name('toko.create');
Route::post('/toko', [TokoController::class, 'store'])->name('toko.store');
Route::put('/toko/{id}', [TokoController::class, 'update'])->name('toko.update');

// Checkout & Orders
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout/pay', [CheckoutController::class, 'pay'])->name('checkout.pay');
Route::get('/riwayat-pesanan', [OrderController::class, 'riwayatPesanan'])->name('riwayat.pesanan');
Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.detail');
Route::post('/orders/{id}/cancel', [OrderController::class, 'cancelForm'])->name('orders.cancel');

// Others (to avoid view errors)
Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.show');
Route::get('/edit_profil', [ProfileController::class, 'edit'])->name('profile.edit');
Route::get('/ratings', [RatingController::class, 'index'])->name('ratings.index');
Route::post('/ratings', [RatingController::class, 'store'])->name('ratings.store');
Route::delete('/ratings/{id}', [RatingController::class, 'destroy'])->name('ratings.destroy');
Route::get('/dashboard', function() { return view('dashboard'); })->name('dashboard');

// API (for specific feature tests)
Route::prefix('api')->group(function() {
    Route::get('/products', [ProductController::class, 'index'])->name('api.products.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
});

require __DIR__.'/auth.php';
