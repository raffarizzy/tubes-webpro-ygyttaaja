<?php

/**
 * TC-WB-F01 — KeranjangController + BarangKeranjangController (Branch Coverage)
 * Feature: FR6 — Keranjang Belanja
 * PIC: Frizam Dafa Maulana
 */

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->user = User::factory()->create(['phone' => '08300000001']);
    $this->product = Product::factory()->create();
});

// ─── KeranjangController::index() ───────────────────────────────────────────

// B1 — tidak login → redirect /login
test('TC-WB-F01 Step 3 — GET /keranjang tanpa login: redirect ke /login', function () {
    $response = $this->get('/keranjang');
    $response->assertRedirect(route('login'));
});

// B2, B3 — login, API aktif → view keranjang berisi data
test('TC-WB-F01 Step 1 — GET /keranjang login dan API aktif: halaman keranjang tampil dengan data', function () {
    Http::fake([
        'http://localhost:3001/api/cart/*' => Http::response(
            ['data' => [['id' => 1, 'product_id' => 1, 'jumlah' => 2]]], 200
        ),
    ]);

    $response = $this->actingAs($this->user)->get('/keranjang');
    $response->assertStatus(200);
    $response->assertViewIs('keranjang');
});

// B2, B4 — login, API gagal → view keranjang dengan cartItems kosong
test('TC-WB-F01 Step 2 — GET /keranjang login dan API mati: halaman keranjang tampil kosong (graceful)', function () {
    Http::fake([
        'http://localhost:3001/api/cart/*' => Http::response([], 500),
    ]);

    $response = $this->actingAs($this->user)->get('/keranjang');
    $response->assertStatus(200);
    $response->assertViewIs('keranjang');
    $response->assertViewHas('keranjangItems', []);
});

// ─── KeranjangController::getCartData() ─────────────────────────────────────

// B5 — tidak login → auth middleware redirect 302 ke /login (bukan JSON 401)
// KeranjangController::getCartData() memiliki Auth::check() namun auth middleware
// mengintersep lebih awal dan melakukan redirect.
test('TC-WB-F01 Step 5 — GET /keranjang/data tanpa login: redirect ke /login (auth middleware)', function () {
    $response = $this->get('/keranjang/data');
    $response->assertRedirect('/login');
});

// B6, B7 — login, API aktif → JSON success
test('TC-WB-F01 Step 4 — GET /keranjang/data login dan API aktif: JSON data keranjang', function () {
    Http::fake([
        'http://localhost:3001/api/cart/*' => Http::response([
            'data' => [
                ['id' => 1, 'product_id' => 1, 'jumlah' => 2, 'harga' => 50000],
            ],
        ], 200),
    ]);

    $response = $this->actingAs($this->user)->getJson('/keranjang/data');
    $response->assertStatus(200);
    $response->assertJson(['success' => true]);
    $response->assertJsonStructure(['data' => ['items', 'total_items', 'total_price']]);
});

// B6, B8 — login, API exception → JSON 500
test('TC-WB-F01 Step 4b — GET /keranjang/data API exception: JSON 500', function () {
    Http::fake([
        'http://localhost:3001/api/cart/*' => Http::response([], 500),
    ]);

    $response = $this->actingAs($this->user)->getJson('/keranjang/data');
    $response->assertStatus(500);
    $response->assertJson(['success' => false]);
});

// ─── KeranjangController::clear() ───────────────────────────────────────────

// B9 — tidak login → JSON 401
test('TC-WB-F01 Step 5b — DELETE /keranjang/clear tanpa login: JSON 401', function () {
    $response = $this->deleteJson('/keranjang/clear');
    $response->assertStatus(401);
});

// B10, B11 — login, clear API berhasil → JSON success
test('TC-WB-F01 Step 6 — DELETE /keranjang/clear login: JSON success keranjang dikosongkan', function () {
    Http::fake([
        'http://localhost:3001/api/cart/*/clear' => Http::response(
            ['message' => 'Cart cleared'], 200
        ),
    ]);

    $response = $this->actingAs($this->user)->deleteJson('/keranjang/clear');
    $response->assertStatus(200);
    $response->assertJson(['success' => true]);
});

// ─── BarangKeranjangController::store() ─────────────────────────────────────

// B13 — validasi gagal (jumlah=-1) → controller menangkap ValidationException di try-catch
// Implementasi mengembalikan JSON 500 (bukan 422) karena validate() dibungkus try-catch \Exception
test('TC-WB-F01 Step 8 — POST /keranjang/item jumlah=-1: validasi gagal (controller returns 500)', function () {
    $response = $this->actingAs($this->user)->postJson('/keranjang/item', [
        'product_id' => $this->product->id,
        'jumlah'     => -1,
    ]);
    // validate() di dalam try-catch \Exception → ValidationException ditangkap → JSON 500
    $response->assertStatus(500);
    $response->assertJson(['success' => false]);
});

// B14, B15 — store valid, API berhasil → JSON success
test('TC-WB-F01 Step 7 — POST /keranjang/item valid: JSON produk ditambahkan ke keranjang', function () {
    Http::fake([
        'http://localhost:3001/api/cart/item' => Http::response(
            ['data' => ['id' => 1]], 200
        ),
    ]);

    $response = $this->actingAs($this->user)->postJson('/keranjang/item', [
        'product_id' => $this->product->id,
        'jumlah'     => 2,
    ]);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);
});

// B14, B16 — store valid, API gagal → JSON failure
test('TC-WB-F01 Step 7b — POST /keranjang/item API gagal: JSON failure', function () {
    Http::fake([
        'http://localhost:3001/api/cart/item' => Http::response(
            ['message' => 'Stok tidak cukup'], 422
        ),
    ]);

    $response = $this->actingAs($this->user)->postJson('/keranjang/item', [
        'product_id' => $this->product->id,
        'jumlah'     => 999,
    ]);

    $response->assertJson(['success' => false]);
});

// ─── BarangKeranjangController::update() ────────────────────────────────────

// B17 — validasi gagal (jumlah=0) → controller menangkap ValidationException di try-catch → JSON 500
test('TC-WB-F01 Step 8b — PUT /keranjang/item/{id} jumlah=0: validasi gagal (controller returns 500)', function () {
    $response = $this->actingAs($this->user)->putJson('/keranjang/item/1', [
        'jumlah' => 0,
    ]);
    $response->assertStatus(500);
    $response->assertJson(['success' => false]);
});

// B18, B19 — update valid, API berhasil → JSON success
test('TC-WB-F01 Step 9 — PUT /keranjang/item/{id} valid: JSON jumlah berhasil diperbarui', function () {
    Http::fake([
        'http://localhost:3001/api/cart/item/*' => Http::response(
            ['data' => ['jumlah' => 5]], 200
        ),
    ]);

    $response = $this->actingAs($this->user)->putJson('/keranjang/item/1', [
        'jumlah' => 5,
    ]);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);
});

// ─── BarangKeranjangController::destroy() ───────────────────────────────────

// B21 — destroy berhasil → JSON success
test('TC-WB-F01 Step 10 — DELETE /keranjang/item/{id} valid: JSON item berhasil dihapus', function () {
    Http::fake([
        'http://localhost:3001/api/cart/item/*' => Http::response(
            ['message' => 'Item dihapus'], 200
        ),
    ]);

    $response = $this->actingAs($this->user)->deleteJson('/keranjang/item/1');
    $response->assertStatus(200);
    $response->assertJson(['success' => true]);
});

// B22 — destroy API gagal → JSON failure
test('TC-WB-F01 Step 10b — DELETE /keranjang/item/{id} API gagal: JSON failure', function () {
    Http::fake([
        'http://localhost:3001/api/cart/item/*' => Http::response([], 500),
    ]);

    $response = $this->actingAs($this->user)->deleteJson('/keranjang/item/1');
    $response->assertJson(['success' => false]);
});
