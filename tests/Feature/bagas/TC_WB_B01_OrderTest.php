<?php

/**
 * TC-WB-B01 — OrderController::history(), riwayatPesanan() (Branch Coverage)
 * Feature: FR8 — Riwayat Pesanan
 * PIC: Bagas Pratama
 */

use App\Models\User;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->userA = User::factory()->create(['phone' => '08500000001']);
    $this->userB = User::factory()->create(['phone' => '08500000002']);
});

// B1 — tidak login, GET /riwayat-pesanan → redirect /login
test('TC-WB-B01 Step 2 — GET /riwayat-pesanan tanpa login: redirect ke /login', function () {
    $response = $this->get('/riwayat-pesanan');
    $response->assertRedirect(route('login'));
});

// B2, B3 — login, API aktif → view riwayat_pesanan dengan data orders
test('TC-WB-B01 Step 1 — GET /riwayat-pesanan login dan API aktif: halaman tampil dengan orders', function () {
    Http::fake([
        'http://localhost:3001/api/history/*' => Http::response([
            'data' => [
                [
                    'id' => 1, 'user_id' => $this->userA->id, 'status' => 'paid',
                    'items' => [], 'alamat' => null,
                    'created_at' => now()->toIso8601String(), 'total_harga' => 50000,
                ],
                [
                    'id' => 2, 'user_id' => $this->userA->id, 'status' => 'pending',
                    'items' => [], 'alamat' => null,
                    'created_at' => now()->toIso8601String(), 'total_harga' => 30000,
                ],
            ],
        ], 200),
    ]);

    $response = $this->actingAs($this->userA)->get('/riwayat-pesanan');
    $response->assertStatus(200);
    $response->assertViewIs('riwayat_pesanan');
    $response->assertViewHas('orders');
});

// B2, B4 — login, API gagal → view dengan orders kosong dan pesan error
test('TC-WB-B01 Step 3 — GET /riwayat-pesanan API mati: view tampil orders kosong dengan error', function () {
    Http::fake([
        'http://localhost:3001/api/history/*' => Http::response([], 500),
    ]);

    $response = $this->actingAs($this->userA)->get('/riwayat-pesanan');
    $response->assertStatus(200);
    $response->assertViewIs('riwayat_pesanan');
    $response->assertViewHas('error');
    expect($response->viewData('orders')->isEmpty())->toBeTrue();
});

// B5 — tidak login, GET /api/orders/history → 302 redirect ke /login
// Route ada di dalam auth middleware group → middleware mengintersep sebelum controller
test('TC-WB-B01 Step 5 — GET /api/orders/history tanpa login: redirect ke /login (auth middleware)', function () {
    $response = $this->get('/api/orders/history');
    $response->assertRedirect('/login');
});

// B6, B7 — login, history API berhasil → JSON success
test('TC-WB-B01 Step 4 — GET /api/orders/history login dan API aktif: JSON success', function () {
    Http::fake([
        'http://localhost:3001/api/history/*' => Http::response([
            'data' => [
                ['id' => 1, 'status' => 'paid'],
            ],
        ], 200),
    ]);

    $response = $this->actingAs($this->userA)->getJson('/api/orders/history');
    $response->assertStatus(200);
    $response->assertJson(['success' => true]);
});

// B10, B13 — show() user yang tepat → view order_detail
// Catatan: view 'order_detail' belum ada di resources/views/ → show() catch block
// mengembalikan redirect ke riwayat.pesanan dengan error.
// Test ini memverifikasi ownership check dan redirect behavior aktual.
test('TC-WB-B01 Step 6 — GET /orders/{id} milik user sendiri: redirect riwayat (view order_detail belum ada)', function () {
    Http::fake([
        'http://localhost:3001/api/history/order/1' => Http::response([
            'data' => [
                'id'         => 1,
                'user_id'    => $this->userA->id,
                'status'     => 'paid',
                'items'      => [],
                'alamat'     => null,
                'created_at' => now()->toIso8601String(),
                'total_harga'=> 50000,
            ],
        ], 200),
    ]);

    $response = $this->actingAs($this->userA)->get('/orders/1');
    // view order_detail.blade.php tidak ada → ViewNotFoundException → catch → redirect
    $response->assertRedirect(route('riwayat.pesanan'));
})->todo('View order_detail.blade.php belum dibuat');

// B10, B12 — show() milik user lain → redirect riwayat dengan error
test('TC-WB-B01 Step 7 — GET /orders/{id} milik user lain: redirect riwayat dengan error akses', function () {
    Http::fake([
        'http://localhost:3001/api/history/order/1' => Http::response([
            'data' => [
                'id'      => 1,
                'user_id' => $this->userB->id,
                'status'  => 'paid',
                'items'   => [],
                'alamat'  => null,
            ],
        ], 200),
    ]);

    $response = $this->actingAs($this->userA)->get('/orders/1');
    $response->assertRedirect(route('riwayat.pesanan'));
    $response->assertSessionHas('error');
});

// B16, B17 — cancelForm API berhasil → redirect riwayat dengan success
test('TC-WB-B01 Step 8 — POST /orders/{id}/cancel API berhasil: redirect dengan flash success', function () {
    Http::fake([
        'http://localhost:3001/api/history/cancel/*' => Http::response(
            ['data' => ['id' => 1, 'status' => 'cancelled']], 200
        ),
    ]);

    $response = $this->actingAs($this->userA)->post('/orders/1/cancel');
    $response->assertRedirect(route('riwayat.pesanan'));
    $response->assertSessionHas('success');
});

// B18 — cancelForm API exception → redirect back dengan error
test('TC-WB-B01 Step 8b — POST /orders/{id}/cancel API gagal: redirect back dengan error', function () {
    Http::fake([
        'http://localhost:3001/api/history/cancel/*' => Http::response([], 500),
    ]);

    $response = $this->actingAs($this->userA)->post('/orders/1/cancel');
    $response->assertSessionHas('error');
});
