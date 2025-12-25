<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\KeranjangController;
use Illuminate\Support\Facades\Route;

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

// OPTION 1: API-first approach (current - tanpa controller, JS fetch dari Node.js API)
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

    //keranjang
    // Tampilkan halaman keranjang
    Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang');
    
    // Tambah item ke keranjang
    Route::post('/keranjang/add', [KeranjangController::class, 'store'])->name('keranjang.add');
    
    // Update jumlah item
    Route::put('/keranjang/item/{itemId}', [KeranjangController::class, 'update'])->name('keranjang.update');
    
    // Hapus item dari keranjang
    Route::delete('/keranjang/item/{itemId}', [KeranjangController::class, 'destroy'])->name('keranjang.delete');
    
    // Kosongkan keranjang
    Route::delete('/keranjang/clear', [KeranjangController::class, 'clear'])->name('keranjang.clear');
    
    // Get total items (untuk badge)
    Route::get('/keranjang/count', [KeranjangController::class, 'getCartCount'])->name('keranjang.count');
});

require __DIR__.'/auth.php';