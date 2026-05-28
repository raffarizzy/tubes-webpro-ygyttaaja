<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TokoController;

// === JSON API routes untuk React SPA ===
Route::get('/api/user', fn() => response()->json(auth()->user()));

Route::middleware('auth')->get('/api/toko', function () {
    $toko = \App\Models\Toko::where('user_id', auth()->id())->first();
    return response()->json($toko);
});

// === Form action routes (tetap, tidak diubah) ===
Route::post('/checkout/pay', [CheckoutController::class, 'pay'])->name('checkout.pay');

Route::get('/payment/success', function () {
    return redirect('/')->with('success', 'Pembayaran berhasil');
})->name('payment.success');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/toko', [TokoController::class, 'store'])->name('toko.store');
    Route::put('/toko/{id}', [TokoController::class, 'update'])->name('toko.update');

    Route::post('/product/store', [ProductController::class, 'store'])->name('product.store');
    Route::put('/product/{id}', [ProductController::class, 'update']);
    Route::delete('/product/{id}', [ProductController::class, 'destroy']);
});

// === Catch-all: semua GET route dilayani React SPA ===
Route::get('/{any}', fn() => view('index'))->where('any', '.*');

require __DIR__.'/auth.php';
