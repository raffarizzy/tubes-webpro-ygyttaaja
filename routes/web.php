<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\BarangKeranjangController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/produk/{id}', function ($id) {
    return view('detail-produk', ['id' => $id]);
})->name('produk.detail');

Route::get('/checkout', function () {
    return view('checkout');
})->name('checkout');

Route::post('/checkout/pay', [CheckoutController::class, 'pay'])
    ->name('checkout.pay');

Route::get('/payment/success', function () {
    return redirect('/')->with('success', 'Pembayaran berhasil');
})->name('payment.success');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Cart routes
    Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang');
    Route::get('/keranjang/data', [KeranjangController::class, 'getCartData'])->name('keranjang.data');
    Route::delete('/keranjang/clear', [KeranjangController::class, 'clear'])->name('keranjang.clear');

    // Cart items routes
    Route::post('/keranjang/item', [BarangKeranjangController::class, 'store'])->name('keranjang.item.store');
    Route::put('/keranjang/item/{id}', [BarangKeranjangController::class, 'update'])->name('keranjang.item.update');
    Route::delete('/keranjang/item/{id}', [BarangKeranjangController::class, 'destroy'])->name('keranjang.item.destroy');
});

// API Routes for products (public access)
Route::get('/api/products', [ProductController::class, 'index'])->name('api.products.index');
Route::get('/api/products/{id}', [ProductController::class, 'show'])->name('api.products.show');

require __DIR__.'/auth.php';