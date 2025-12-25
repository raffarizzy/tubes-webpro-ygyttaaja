<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AlamatController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// ============================================
// PUBLIC ROUTES
// ============================================

Route::get('/', function () {
    return view('homepage');
})->name('home');

Route::get('/produk/{id}', function ($id) {
    return view('detail-produk', ['id' => $id]);
})->name('produk.detail');

// ============================================
// AUTH REQUIRED ROUTES
// ============================================

Route::middleware('auth')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Keranjang
    Route::get('/keranjang', function () {
        return view('keranjang');
    })->name('keranjang');
    
    // Checkout
    Route::get('/checkout', function () {
        return view('checkout');
    })->name('checkout');
    
    Route::post('/checkout/pay', [CheckoutController::class, 'pay'])
        ->name('checkout.pay');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Alamat Management
    Route::get('/alamat', [AlamatController::class, 'index'])->name('alamat.index');
    Route::post('/alamat', [AlamatController::class, 'store'])->name('alamat.store');
    Route::put('/alamat/{id}', [AlamatController::class, 'update'])->name('alamat.update');
    Route::delete('/alamat/{id}', [AlamatController::class, 'destroy'])->name('alamat.destroy');
    
    // Orders (dari web, bukan API)
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/history', [OrderController::class, 'history'])->name('orders.history');
});

// ============================================
// PAYMENT CALLBACK
// ============================================

Route::get('/payment/success', function () {
    return redirect('/')->with('success', 'Pembayaran berhasil');
})->name('payment.success');

Route::get('/payment/failed', function () {
    return redirect('/checkout')->with('error', 'Pembayaran gagal');
})->name('payment.failed');

require __DIR__.'/auth.php';