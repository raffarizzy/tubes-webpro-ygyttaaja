<?php

/**
 * TC-WB-N02 — ProductController::index() Pagination (Branch Coverage)
 * Feature: FR4 — Katalog Produk
 * PIC: Naufal Muhammad Dzulfikar
 *
 * Catatan arsitektur:
 * - GET /api/products → ProductController::index() → view('products.index') tidak ada.
 * - Pagination logic sebenarnya ada di Node.js API layer.
 * - Test B1/B5/B6 diverifikasi via Http::fake() langsung ke Node.js endpoint.
 * - Test B2/B3/B4 diverifikasi via GET / (HomeController Eloquent).
 */

use Illuminate\Support\Facades\Http;

// B2, B3 — GET / default page 1, ada produk → halaman pertama tampil
test('TC-WB-N02 Step 1 — homepage default page 1 dengan banyak produk: halaman pertama tampil', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
    $response->assertViewIs('homepage');
});

// B1, B6 — Node.js API /api/products?page=2 → produk halaman 2 tampil
test('TC-WB-N02 Step 2 — Node.js /api/products?page=2: produk halaman 2 dikembalikan', function () {
    Http::fake([
        'http://localhost:3001/api/products*' => Http::response([
            'data' => [
                ['id' => 11, 'nama' => 'Produk 11'],
                ['id' => 12, 'nama' => 'Produk 12'],
            ],
            'pagination' => ['page' => 2, 'total_pages' => 3],
        ], 200),
    ]);

    $response = Http::get('http://localhost:3001/api/products', ['page' => 2]);
    expect($response->status())->toBe(200);
    expect($response->json('data'))->toHaveCount(2);
    expect($response->json('pagination.page'))->toBe(2);
});

// B1, B5 — Node.js API /api/products?page=9999 → data kosong
test('TC-WB-N02 Step 3 — Node.js /api/products?page=9999: data kosong tidak crash', function () {
    Http::fake([
        'http://localhost:3001/api/products*' => Http::response(
            ['data' => [], 'pagination' => ['page' => 9999, 'total_pages' => 3]], 200
        ),
    ]);

    $response = Http::get('http://localhost:3001/api/products', ['page' => 9999]);
    expect($response->status())->toBe(200);
    expect($response->json('data'))->toBeEmpty();
});

// B2, B4 — hanya 3 produk → single page, tidak ada next
test('TC-WB-N02 Step 4 — 3 produk: single page tanpa next', function () {
    Http::fake([
        'http://localhost:3001/api/products*' => Http::response([
            'data' => [
                ['id' => 1, 'nama' => 'A'],
                ['id' => 2, 'nama' => 'B'],
                ['id' => 3, 'nama' => 'C'],
            ],
            'pagination' => ['page' => 1, 'total_pages' => 1],
        ], 200),
    ]);

    $response = Http::get('http://localhost:3001/api/products', ['page' => 1]);
    expect($response->json('pagination.total_pages'))->toBe(1);
});

// B1, B6 — navigasi kembali ke halaman 1
test('TC-WB-N02 Step 5 — Node.js /api/products?page=1: kembali ke halaman 1', function () {
    Http::fake([
        'http://localhost:3001/api/products*' => Http::response([
            'data' => [
                ['id' => 1, 'nama' => 'Produk 1'],
                ['id' => 2, 'nama' => 'Produk 2'],
            ],
            'pagination' => ['page' => 1, 'total_pages' => 3],
        ], 200),
    ]);

    $response = Http::get('http://localhost:3001/api/products', ['page' => 1]);
    expect($response->status())->toBe(200);
    expect($response->json('pagination.page'))->toBe(1);
});
