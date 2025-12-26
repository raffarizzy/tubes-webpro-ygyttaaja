<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// ============================================
// PUBLIC API ROUTES
// ============================================

// Products (public - semua orang bisa akses)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// ============================================
// PROTECTED API ROUTES (Admin/Seller only)
// ============================================

Route::middleware('auth:sanctum')->group(function () {
    // Product Management (hanya untuk admin/seller)
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
});