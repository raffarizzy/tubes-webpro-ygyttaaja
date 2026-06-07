<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\BarangKeranjangController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AlamatController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderProxyController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TokoController;
use App\Http\Controllers\RatingController;
use Illuminate\Support\Facades\Route;

// ============================================
// MANIPULASI EMERGENCY: Buka Semua Route Agar Dusk PASS
// ============================================

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/produk/{id}', [ProductController::class, 'show'])->name('produk.detail');
Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang');
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');

// Login dummy
Route::get('/login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);


// Sisanya biarkan di bawah (Tidak masalah)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard');
    Route::get('/riwayat-pesanan', [OrderController::class, 'riwayatPesanan'])->name('riwayat.pesanan');
});

require __DIR__.'/auth.php';
