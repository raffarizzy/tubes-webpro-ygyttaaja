<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TokoController;


Route::get('/', function () {
    return view('homepage');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/keranjang', function () {
    return view('keranjang');
})->name('keranjang');

Route::get('/checkout', function () {
    return view('checkout');
})->name('checkout');

Route::get('/produk/{id}', function ($id) {
    return view('detail-produk', ['id' => $id]);
})->name('produk.detail');


// routes/web.php
Route::post('/checkout/pay', [CheckoutController::class, 'pay'])
    ->name('checkout.pay');

Route::get('/payment/success', function () {
    return redirect('/')->with('success', 'Pembayaran berhasil');
})->name('payment.success');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('auth')->group(function () {
    Route::get('/toko', [TokoController::class, 'index']) ->name('profil_toko');
    Route::get('/toko/create', [TokoController::class, 'create'])->name('toko.create');
    Route::post('/toko', [TokoController::class, 'store'])->name('toko.store');
    Route::put('/toko/{id}', [TokoController::class, 'update'])->name('toko.update');
    });

    Route::post('/product/store', [ProductController::class, 'store'])->name('product.store');
    Route::put('/product/{id}', [ProductController::class, 'update']);
    Route::delete('/product/{id}', [ProductController::class, 'destroy']);
});

require __DIR__.'/auth.php';
