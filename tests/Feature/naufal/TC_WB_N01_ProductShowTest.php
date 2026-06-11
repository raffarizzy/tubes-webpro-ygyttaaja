<?php

/**
 * TC-WB-N01 — ProductController::show() Fallback Node-Eloquent (Path Coverage)
 * Feature: FR5 — Detail Produk
 * PIC: Naufal Muhammad Dzulfikar
 */

use App\Models\Category;
use App\Models\Product;
use App\Models\Rating;
use App\Models\Toko;
use App\Models\User;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $user = User::factory()->create(['phone' => '08400000001']);
    $category = Category::factory()->create();
    $toko = Toko::create([
        'user_id'       => $user->id,
        'nama_toko'     => 'Toko Test',
        'deskripsi_toko'=> 'Deskripsi',
        'lokasi'        => 'Jakarta',
        'logo_path'     => null,
    ]);
    $this->product = Product::factory()->create([
        'toko_id'     => $toko->id,
        'category_id' => $category->id,
    ]);
});

// B4, B6 — Node API aktif, produk ada → view detail-produk lengkap
test('TC-WB-N01 Step 1 — Node API aktif, produk ada: halaman detail tampil lengkap', function () {
    Http::fake([
        "http://localhost:3001/api/products/{$this->product->id}" => Http::response([
            'data' => [
                'id'        => $this->product->id,
                'nama'      => $this->product->nama,
                'harga'     => $this->product->harga,
                'stok'      => $this->product->stok,
                'deskripsi' => $this->product->deskripsi,
                'diskon'    => 0,
                'imagePath' => 'produk/default.jpg',
            ],
        ], 200),
    ]);

    $response = $this->get("/produk/{$this->product->id}");
    $response->assertStatus(200);
    $response->assertViewIs('detail-produk');
});

// B4, B5 — Node API aktif, productData null (produk tidak ada di API) → 404
test('TC-WB-N01 Step 2 — Node API aktif, produk tidak ditemukan di API: HTTP 404', function () {
    Http::fake([
        'http://localhost:3001/api/products/99999' => Http::response(
            ['data' => null], 200
        ),
    ]);

    $response = $this->get('/produk/99999');
    $response->assertStatus(404);
});

// B7, B9 — exception (timeout), Eloquent ada produk → fallback, view detail-produk
test('TC-WB-N01 Step 3 — Node API timeout, Eloquent ada produk: fallback Eloquent berhasil', function () {
    Http::fake([
        "http://localhost:3001/api/products/{$this->product->id}" => function () {
            throw new \Illuminate\Http\Client\ConnectionException('Connection refused');
        },
    ]);

    $response = $this->get("/produk/{$this->product->id}");
    $response->assertStatus(200);
    $response->assertViewIs('detail-produk');
});

// B7, B8 — exception, Eloquent juga tidak ada → 404
test('TC-WB-N01 Step 4 — Node API timeout, produk tidak ada di Eloquent: HTTP 404', function () {
    Http::fake([
        'http://localhost:3001/api/products/99999' => function () {
            throw new \Illuminate\Http\Client\ConnectionException('Connection refused');
        },
    ]);

    $response = $this->get('/produk/99999');
    $response->assertStatus(404);
});

// B1, B3 — Node API return 500, Eloquent ada produk → fallback, view detail-produk
test('TC-WB-N01 Step 5 — Node API return 500, Eloquent ada produk: fallback tampil', function () {
    Http::fake([
        "http://localhost:3001/api/products/{$this->product->id}" => Http::response([], 500),
    ]);

    $response = $this->get("/produk/{$this->product->id}");
    $response->assertStatus(200);
    $response->assertViewIs('detail-produk');
});

// B6 (ratings) — produk dengan beberapa rating → daftar rating dan avg tampil
test('TC-WB-N01 Step 6 — produk dengan rating: avg dan jumlah ulasan tampil di view', function () {
    $user2 = User::factory()->create(['phone' => '08400000099']);
    Rating::create(['product_id' => $this->product->id, 'user_id' => $this->product->toko->user_id, 'rating' => 4, 'review' => 'Bagus']);
    Rating::create(['product_id' => $this->product->id, 'user_id' => $user2->id, 'rating' => 2, 'review' => 'Kurang']);

    Http::fake([
        "http://localhost:3001/api/products/{$this->product->id}" => Http::response([
            'data' => [
                'id'        => $this->product->id,
                'nama'      => $this->product->nama,
                'harga'     => 50000,
                'stok'      => 10,
                'deskripsi' => 'Test',
                'diskon'    => 0,
                'imagePath' => 'produk/default.jpg',
            ],
        ], 200),
    ]);

    $response = $this->get("/produk/{$this->product->id}");
    $response->assertStatus(200);
    $response->assertViewHas('avgRating', 3.0);
    $response->assertViewHas('ratingCount', 2);
});
