<?php

/**
 * TC-WB-B02 — CheckoutController::index(), pay(), callback (Branch Coverage)
 * Feature: FR7 — Melakukan Pemesanan
 * PIC: Bagas Pratama
 */

use App\Models\User;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->userA = User::factory()->create(['phone' => '08600000001']);
    $this->userB = User::factory()->create(['phone' => '08600000002']);
});

// B1 — tidak login, GET /checkout → redirect /login
test('TC-WB-B02 Step 2 — GET /checkout tanpa login: redirect ke /login', function () {
    $response = $this->get('/checkout');
    $response->assertRedirect(route('login'));
});

// B2, B4, B6 — login, kedua API (alamat + cart) berhasil → view checkout lengkap
test('TC-WB-B02 Step 1 — GET /checkout login, API aktif: halaman checkout tampil lengkap', function () {
    Http::fake([
        'http://localhost:3001/api/alamat/*' => Http::response([
            'data' => [['id' => 1, 'label' => 'Rumah', 'alamat' => 'Jl. Test']],
        ], 200),
        'http://localhost:3001/api/cart/*' => Http::response([
            'data' => [
                'items' => [
                    [
                        'id' => 1, 'product_id' => 1, 'jumlah' => 2, 'subtotal' => 100000,
                        'product' => ['id' => 1, 'nama' => 'Produk A', 'harga' => 50000, 'imagePath' => null, 'deskripsi' => 'test'],
                    ],
                ],
            ],
        ], 200),
    ]);

    $response = $this->actingAs($this->userA)->get('/checkout');
    $response->assertStatus(200);
    $response->assertViewIs('checkout');
    $response->assertViewHas('alamats');
    $response->assertViewHas('cartItems');
});

// B2, B3, B6 — alamat API gagal, cart API berhasil → view dengan alamats kosong
test('TC-WB-B02 Step 3 — GET /checkout alamat API gagal: halaman tampil dengan alamat kosong', function () {
    Http::fake([
        'http://localhost:3001/api/alamat/*' => Http::response([], 500),
        'http://localhost:3001/api/cart/*'   => Http::response([
            'data' => ['items' => []],
        ], 200),
    ]);

    $response = $this->actingAs($this->userA)->get('/checkout');
    $response->assertStatus(200);
    $response->assertViewIs('checkout');
    expect($response->viewData('alamats')->isEmpty())->toBeTrue();
});

// B2, B5 — cart API gagal → view dengan error
test('TC-WB-B02 Step 4 — GET /checkout cart API gagal: view dengan pesan error keranjang', function () {
    Http::fake([
        'http://localhost:3001/api/alamat/*' => Http::response(
            ['data' => []], 200
        ),
        'http://localhost:3001/api/cart/*'   => Http::response([], 500),
    ]);

    $response = $this->actingAs($this->userA)->get('/checkout');
    $response->assertStatus(200);
    $response->assertViewHas('error');
});

// B7 — tidak login, POST /checkout/pay → JSON 401
test('TC-WB-B02 Step 6 — POST /checkout/pay tanpa login: JSON 401 Unauthorized', function () {
    $response = $this->postJson('/checkout/pay', [
        'order_id' => 1,
        'alamat_id'=> 1,
        'total'    => 100000,
    ]);
    // auth:sanctum middleware returns {"message": "Unauthenticated."} not {"success": false}
    $response->assertStatus(401);
});

// B8, B9 — login, validasi gagal (order_id tidak ada) → JSON 422
test('TC-WB-B02 Step 7 — POST /checkout/pay tanpa order_id: JSON 422', function () {
    $response = $this->actingAs($this->userA)->postJson('/checkout/pay', [
        'alamat_id' => 1,
        'total'     => 100000,
    ]);
    $response->assertStatus(422);
});

// B13 — order milik user lain → JSON 403
test('TC-WB-B02 Step 8 — POST /checkout/pay dengan order milik user lain: JSON 403', function () {
    Http::fake([
        'http://localhost:3001/api/orders/99' => Http::response([
            'data' => [
                'id'        => 99,
                'user_id'   => $this->userB->id,
                'status'    => 'pending',
                'alamat_id' => 1,
            ],
        ], 200),
    ]);

    $response = $this->actingAs($this->userA)->postJson('/checkout/pay', [
        'order_id'  => 99,
        'alamat_id' => 1,
        'total'     => 100000,
    ]);
    $response->assertStatus(403);
    $response->assertJson(['success' => false]);
});

// B15 — order status bukan pending → exception/500
test('TC-WB-B02 Step 9 — POST /checkout/pay order sudah paid: error 500 sudah diproses', function () {
    Http::fake([
        'http://localhost:3001/api/orders/88' => Http::response([
            'data' => [
                'id'        => 88,
                'user_id'   => $this->userA->id,
                'status'    => 'paid',
                'alamat_id' => 1,
            ],
        ], 200),
    ]);

    $response = $this->actingAs($this->userA)->postJson('/checkout/pay', [
        'order_id'  => 88,
        'alamat_id' => 1,
        'total'     => 100000,
    ]);
    $response->assertStatus(500);
    $response->assertJson(['success' => false]);
});

// B11 — order tidak ditemukan di Node API → exception/500
test('TC-WB-B02 Step 5b — POST /checkout/pay order tidak ada di API: error 500', function () {
    Http::fake([
        'http://localhost:3001/api/orders/*' => Http::response([], 404),
    ]);

    $response = $this->actingAs($this->userA)->postJson('/checkout/pay', [
        'order_id'  => 77,
        'alamat_id' => 1,
        'total'     => 100000,
    ]);
    $response->assertStatus(500);
    $response->assertJson(['success' => false]);
});

// B19 — paymentSuccess dengan session last_order_id → session di-clear, redirect riwayat
test('TC-WB-B02 Step 10 — GET /payment/success dengan session last_order_id: redirect riwayat dengan flash success', function () {
    $response = $this->actingAs($this->userA)
        ->withSession(['last_order_id' => 1])
        ->get('/payment/success');

    $response->assertRedirect(route('riwayat.pesanan'));
    $response->assertSessionHas('success');
    $response->assertSessionMissing('last_order_id');
});

// B20 — paymentSuccess tanpa session → langsung redirect riwayat
test('TC-WB-B02 Step 10b — GET /payment/success tanpa session: redirect riwayat pesanan', function () {
    $response = $this->actingAs($this->userA)->get('/payment/success');
    $response->assertRedirect(route('riwayat.pesanan'));
});
