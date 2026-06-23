<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Rating;
use App\Models\Toko;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * TC-WB-N01 — ProductController::show() Fallback Node→Eloquent (Path Coverage)
 * PIC: Naufal Muhammad Dzulfikar
 *
 * Peta jalur show($id):
 *   try:
 *     [B1] response gagal (non-2xx) → fallback Eloquent
 *          ├─ [B2] produk null → abort 404
 *          └─ [B3] produk ada  → view detail-produk
 *     [B4] response sukses (2xx)
 *          ├─ [B5] data null → abort 404
 *          └─ [B6] data ada  → bangun objek + load ratings + view detail-produk
 *   catch (exception koneksi/timeout):
 *     [B7] fallback Eloquent
 *          ├─ [B8] produk null → abort 404
 *          └─ [B9] produk ada  → view detail-produk
 *
 * Node API di-fake dengan Http::fake() agar deterministik (tanpa server Node nyata).
 */
class ProductShowWhiteBoxTest extends TestCase
{
    use RefreshDatabase;

    /** Buat produk lengkap (toko + kategori) di DB untuk jalur fallback Eloquent. */
    private function makeProduct(array $overrides = []): Product
    {
        $user = User::forceCreate([
            'name'     => 'Seller WB',
            'email'    => 'seller_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'phone'    => '0812' . random_int(1000000, 9999999),
        ]);

        $toko = Toko::forceCreate([
            'user_id'        => $user->id,
            'nama_toko'      => 'Toko WB',
            'deskripsi_toko' => 'Deskripsi toko untuk pengujian',
            'lokasi'         => 'Jakarta',
        ]);

        $category = Category::forceCreate(['judulKategori' => 'Oli']);

        return Product::forceCreate(array_merge([
            'toko_id'     => $toko->id,
            'category_id' => $category->id,
            'nama'        => 'Oli Mesin DB',
            'deskripsi'   => 'Deskripsi produk',
            'harga'       => 75000,
            'diskon'      => 0,
            'stok'        => 10,
            'berat'       => 1000,
            'imagePath'   => 'produk/default.png',
        ], $overrides));
    }

    /** Payload produk seperti respons Node API (untuk Http::fake). */
    private function fakeApiProduct(int $id = 1): array
    {
        return [
            'id'                 => $id,
            'nama'               => 'Oli Mesin API',
            'harga'              => 75000,
            'diskon'             => 0,
            'stok'               => 10,
            'berat'              => 1000,
            'deskripsi'          => 'Deskripsi dari API',
            'imagePath'          => 'produk/default.png',
            'toko_id'            => 1,
            'nama_toko'          => 'Toko API',
            'toko_lokasi'        => 'Bandung',
            'toko_logo'          => null,
            'is_verified_seller' => true, // set agar tidak perlu lookup Toko ke DB
            'category_id'        => 1,
            'category_nama'      => 'Oli',
        ];
    }

    /** B4,B6 — Node API sukses & data ada: tampil detail dari API. */
    public function test_b4_b6_node_sukses_data_ada_tampil_detail(): void
    {
        Http::fake(['*' => Http::response(['success' => true, 'data' => $this->fakeApiProduct(1)], 200)]);

        $response = $this->get('/produk/1');

        $response->assertOk();
        $response->assertViewIs('detail-produk');
        $response->assertViewHas('product');
    }

    /** B4,B5 — Node API sukses tapi data kosong: abort 404. */
    public function test_b4_b5_node_sukses_data_null_abort_404(): void
    {
        Http::fake(['*' => Http::response(['success' => true, 'data' => null], 200)]);

        $this->get('/produk/999')->assertNotFound();
    }

    /** B1,B3 — Node API gagal (HTTP 500), fallback Eloquent menemukan produk. */
    public function test_b1_b3_node_gagal_fallback_eloquent_tampil_detail(): void
    {
        Http::fake(['*' => Http::response('Server Error', 500)]);
        $product = $this->makeProduct();

        $response = $this->get('/produk/' . $product->id);

        $response->assertOk();
        $response->assertViewIs('detail-produk');
    }

    /** B1,B2 — Node API gagal (HTTP 500), produk tidak ada di DB: abort 404. */
    public function test_b1_b2_node_gagal_produk_tidak_ada_abort_404(): void
    {
        Http::fake(['*' => Http::response('Server Error', 500)]);

        $this->get('/produk/123456')->assertNotFound();
    }

    /** B7,B9 — Node API timeout/exception, fallback Eloquent menemukan produk. */
    public function test_b7_b9_node_exception_fallback_eloquent_tampil_detail(): void
    {
        Http::fake(function () {
            throw new ConnectionException('Connection timed out');
        });
        $product = $this->makeProduct();

        $response = $this->get('/produk/' . $product->id);

        $response->assertOk();
        $response->assertViewIs('detail-produk');
    }

    /** B7,B8 — Node API timeout/exception, produk tidak ada di DB: abort 404. */
    public function test_b7_b8_node_exception_produk_tidak_ada_abort_404(): void
    {
        Http::fake(function () {
            throw new ConnectionException('Connection timed out');
        });

        $this->get('/produk/123456')->assertNotFound();
    }

    
}
