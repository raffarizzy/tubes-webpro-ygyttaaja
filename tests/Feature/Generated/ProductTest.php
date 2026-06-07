<?php

namespace Tests\Feature\Generated;

use App\Models\Product;
use App\Models\Toko;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    /**
     * TC-WB-N01 — Branch B2 (Success from API)
     * Show product details using data from Node.js API.
     */
    public function test_show_product_details_from_api(): void
    {
        Http::fake([
            'http://localhost:3001/api/products/1' => Http::response([
                'success' => true,
                'data' => [
                    'id' => 1,
                    'nama' => 'API Product',
                    'harga' => 10000,
                    'stok' => 5,
                    'imagePath' => 'api.png',
                    'deskripsi' => 'API Desc',
                    'nama_toko' => 'API Toko',
                    'category_nama' => 'API Cat'
                ]
            ], 200),
        ]);

        $this->withoutExceptionHandling();
        $response = $this->get('/produk/1');

        $response->assertStatus(200);
        $response->assertViewIs('detail-produk');
        $response->assertViewHas('product', function($p) {
            return $p->nama === 'API Product' && $p->toko->nama_toko === 'API Toko';
        });
    }

    /**
     * TC-WB-N01 — Branch B1 (Fallback to Eloquent)
     * API fails, fallback to Laravel database.
     */
    public function test_show_product_details_fallback_to_eloquent(): void
    {
        $user = User::factory()->create();
        $toko = Toko::create([
            'user_id' => $user->id,
            'nama_toko' => 'Eloquent Toko',
            'deskripsi_toko' => 'Desc',
            'lokasi' => 'Loc',
            'logo_path' => 'logo.png'
        ]);
        
        $cat = new Category();
        $cat->judulKategori = 'Eloquent Cat';
        $cat->save();
        
        $product = new Product();
        $product->id = 10;
        $product->toko_id = $toko->id;
        $product->category_id = $cat->id;
        $product->nama = 'Eloquent Product';
        $product->harga = 20000;
        $product->stok = 10;
        $product->deskripsi = 'Eloquent Desc';
        $product->imagePath = 'eloquent.png';
        $product->save();

        // Mock API failure
        Http::fake([
            'http://localhost:3001/api/products/10' => Http::response([], 500),
        ]);

        $response = $this->get('/produk/10');

        $response->assertStatus(200);
        $response->assertViewHas('product', function($p) use ($product) {
            return $p->id === $product->id && $p->nama === 'Eloquent Product';
        });
    }

    /**
     * TC-WB-N02 — Branch B3 (Success)
     * Store product via API.
     */
    public function test_store_product_via_api(): void
    {
        $user = User::factory()->create();
        $toko = Toko::create([
            'user_id' => $user->id,
            'nama_toko' => 'User Toko',
            'deskripsi_toko' => 'Desc',
            'lokasi' => 'Loc',
            'logo_path' => 'logo.png'
        ]);
        $this->actingAs($user);

        Http::fake([
            'http://localhost:3001/api/products' => Http::response(['success' => true], 201),
        ]);

        $response = $this->postJson('/product/store', [
            'nama' => 'New Product',
            'category_id' => 1,
            'harga' => 15000,
            'stok' => 10,
            'deskripsi' => 'New Desc',
            'image' => \Illuminate\Http\UploadedFile::fake()->image('p.jpg')
        ]);

        $response->assertStatus(200); // Controller returns 200 on API 201 success
        $response->assertJsonPath('success', true);
    }
}
