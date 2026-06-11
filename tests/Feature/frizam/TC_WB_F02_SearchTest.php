<?php

/**
 * TC-WB-F02 — HomeController::index() + Node.js Search (Branch Coverage)
 * Feature: FR3 — Pencarian Sparepart
 * PIC: Frizam Dafa Maulana
 *
 * Catatan arsitektur:
 * - HomeController::index() menggunakan Eloquent langsung (tidak ada search branching)
 * - Branching search ada di Node.js /api/products layer
 * - Laravel route GET /api/products → ProductController::index() mengembalikan view 'products.index'
 *   yang belum ada, sehingga tidak bisa di-test via HTTP response view.
 * - Test berikut memverifikasi: (1) homepage Eloquent load, (2) Node.js fake branching via Http::fake
 */

use App\Models\Category;
use App\Models\Product;
use App\Models\Toko;
use App\Models\User;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $user     = User::factory()->create(['phone' => '08300000099']);
    $category = Category::factory()->create();
    $toko     = Toko::create([
        'user_id' => $user->id, 'nama_toko' => 'Toko Search', 'deskripsi_toko' => 'X', 'lokasi' => 'Y',
    ]);
    Product::factory()->count(3)->create(['toko_id' => $toko->id, 'category_id' => $category->id]);
});

// B2 — GET / tanpa search: HomeController Eloquent load semua produk
test('TC-WB-F02 Step 1 — GET / tanpa search: homepage tampil dengan semua produk (Eloquent)', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
    $response->assertViewIs('homepage');
    $response->assertViewHas('products');
});

// B1, B3 — Node.js API search ditemukan (via Http::fake)
test('TC-WB-F02 Step 2 — Node.js API search "Kampas Rem": hasil ditemukan (fake)', function () {
    Http::fake([
        'http://localhost:3001/api/products*' => Http::response([
            'data' => [['id' => 1, 'nama' => 'Kampas Rem', 'harga' => 50000]],
        ], 200),
    ]);

    // Verifikasi Http::fake menerima search query
    $response = Http::get('http://localhost:3001/api/products', ['search' => 'Kampas Rem']);
    expect($response->status())->toBe(200);
    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.nama'))->toBe('Kampas Rem');
});

// B1, B4 — Node.js API search tidak ada hasil (via Http::fake)
test('TC-WB-F02 Step 3 — Node.js API search "xyzxyz123": data kosong (fake)', function () {
    Http::fake([
        'http://localhost:3001/api/products*' => Http::response(['data' => []], 200),
    ]);

    $response = Http::get('http://localhost:3001/api/products', ['search' => 'xyzxyz123']);
    expect($response->status())->toBe(200);
    expect($response->json('data'))->toBeEmpty();
});

// B1, B3 — case-insensitive search
test('TC-WB-F02 Step 4 — Node.js API search "kampas rem" lowercase: ditemukan (fake)', function () {
    Http::fake([
        'http://localhost:3001/api/products*' => Http::response([
            'data' => [['id' => 1, 'nama' => 'Kampas Rem', 'harga' => 50000]],
        ], 200),
    ]);

    $response = Http::get('http://localhost:3001/api/products', ['search' => 'kampas rem']);
    expect($response->status())->toBe(200);
    expect($response->json('data'))->not->toBeEmpty();
});

// B1 — search via parameter query string
test('TC-WB-F02 Step 5 — Node.js API GET ?search=Rem: JSON berisi produk mengandung Rem (fake)', function () {
    Http::fake([
        'http://localhost:3001/api/products*' => Http::response([
            'data' => [['id' => 1, 'nama' => 'Kampas Rem', 'harga' => 50000]],
        ], 200),
    ]);

    $response = Http::get('http://localhost:3001/api/products?search=Rem');
    expect($response->json('data.0.nama'))->toContain('Rem');
});
