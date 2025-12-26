<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\BarangKeranjangController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AlamatController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TokoController;


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
    
    // Profile Routes
    Route::get('/profile', function () {
        return view('profil');
    })->name('profile.show');
    
    Route::get('/edit_profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // ============================================
    // RIWAYAT PESANAN - HALAMAN VIEW
    // ============================================
    Route::get('/riwayat-pesanan', function () {
        return view('riwayat_pesanan');
    })->name('riwayat.pesanan');
    
    // ============================================
    // ORDERS API - Dengan prefix /api
    // ============================================
    // Get history orders (API endpoint untuk JavaScript)
    Route::get('/api/orders/history', [OrderController::class, 'history'])->name('orders.history');
    
    // Create order (checkout)
    Route::post('/api/orders', [OrderController::class, 'store'])->name('orders.store');
    
    // Cancel order
    Route::post('/api/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    
    // Detail order
    Route::get('/api/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    
    // ============================================
    // ALAMAT MANAGEMENT
    // ============================================
    Route::get('/alamat', [AlamatController::class, 'index'])->name('alamat.index');
    Route::post('/alamat', [AlamatController::class, 'store'])->name('alamat.store');
    Route::put('/alamat/{id}', [AlamatController::class, 'update'])->name('alamat.update');
    Route::delete('/alamat/{id}', [AlamatController::class, 'destroy'])->name('alamat.destroy');
});

// ============================================
// PAYMENT CALLBACK
// ============================================

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
Route::get('/payment/failed', function () {
    return redirect('/checkout')->with('error', 'Pembayaran gagal');
})->name('payment.failed');

require __DIR__.'/auth.php';