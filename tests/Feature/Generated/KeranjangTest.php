<?php

namespace Tests\Feature\Generated;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class KeranjangTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    /**
     * TC-WB-F01 — Branch B2 (Success)
     * Show cart items from Node.js API.
     */
    public function test_index_shows_cart_items(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Http::fake([
            'http://localhost:3001/api/cart/*' => Http::response([
                'success' => true,
                'data' => [
                    [
                        'id' => 1,
                        'product_id' => 101,
                        'jumlah' => 2,
                        'harga' => 25000,
                        'product' => ['nama' => 'Test Product']
                    ]
                ]
            ], 200),
        ]);

        $response = $this->get('/keranjang');

        $response->assertStatus(200);
        $response->assertViewIs('keranjang');
        $response->assertViewHas('keranjangItems', function($items) {
            return count($items) === 1 && $items[0]['jumlah'] === 2;
        });
    }

    /**
     * TC-WB-F01 — Branch B1 (Empty/Failure)
     * Show empty cart if API fails.
     */
    public function test_index_shows_empty_cart_if_api_fails(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Http::fake([
            'http://localhost:3001/api/cart/*' => Http::response([], 500),
        ]);

        $response = $this->get('/keranjang');

        $response->assertStatus(200);
        $response->assertViewHas('keranjangItems', []);
    }

    /**
     * TC-WB-F02 — Branch B1 (JSON Success)
     * Get cart data as JSON with calculated totals.
     */
    public function test_get_cart_data_json_success(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Http::fake([
            'http://localhost:3001/api/cart/*' => Http::response([
                'success' => true,
                'data' => [
                    ['id' => 1, 'harga' => 10000, 'jumlah' => 2],
                    ['id' => 2, 'harga' => 5000, 'jumlah' => 1]
                ]
            ], 200),
        ]);

        $response = $this->getJson('/keranjang/data');

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.total_items', 3);
        $response->assertJsonPath('data.total_price', 25000);
    }

    /**
     * TC-WB-F03 — Branch B1 (Clear Success)
     * Clear cart successfully.
     */
    public function test_clear_cart_success(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Http::fake([
            'http://localhost:3001/api/cart/*/clear' => Http::response([
                'success' => true
            ], 200),
        ]);

        $response = $this->deleteJson('/keranjang/clear');

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('message', 'Keranjang berhasil dikosongkan');
    }
}
