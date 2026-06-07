<?php

namespace Tests\Feature\Generated;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    /**
     * TC-WB-B01 — Branch B1 (Success)
     * Create order via Node.js API.
     */
    public function test_store_order_success(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Http::fake([
            'http://localhost:3001/api/orders' => Http::response([
                'success' => true,
                'data' => ['id' => 123]
            ], 201),
        ]);

        $response = $this->postJson('/api/orders', [
            'alamat_id' => 1,
            'items' => [
                ['product_id' => 10, 'jumlah' => 1]
            ]
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.id', 123);
    }

    /**
     * TC-WB-B01 — Branch B2 (History)
     * Show order history from Node.js API.
     */
    public function test_riwayat_pesanan_shows_orders(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Http::fake([
            'http://localhost:3001/api/history/*' => Http::response([
                'success' => true,
                'data' => [
                    [
                        'id' => 1,
                        'user_id' => $user->id,
                        'total_harga' => 50000,
                        'status' => 'paid',
                        'created_at' => '2023-01-01 10:00:00',
                        'items' => [
                            [
                                'id' => 1,
                                'qty' => 1,
                                'harga' => 50000,
                                'subtotal' => 50000,
                                'nama_produk' => 'Test Product',
                                'product' => ['id' => 1, 'nama' => 'Test Product', 'harga' => 50000, 'image_path' => 'img.png', 'deskripsi' => 'Desc']
                            ]
                        ],
                        'alamat' => [
                            'nama_penerima' => 'Raffa',
                            'alamat' => 'Bandung',
                            'nomor_penerima' => '08123'
                        ]
                    ]
                ]
            ], 200),
        ]);

        $response = $this->get('/riwayat-pesanan');

        $response->assertStatus(200);
        $response->assertViewIs('riwayat_pesanan');
        $response->assertViewHas('orders', function($orders) {
            return $orders->count() === 1 && $orders->first()->status === 'paid';
        });
    }

    /**
     * TC-WB-B01 — Branch B3 (Cancel Success)
     * Cancel order redirects with success message.
     */
    public function test_cancel_order_success(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Http::fake([
            'http://localhost:3001/api/history/cancel/*' => Http::response([
                'success' => true
            ], 200),
        ]);

        $response = $this->post('/orders/1/cancel');

        $response->assertRedirect('/riwayat-pesanan');
        $response->assertSessionHas('success', 'Pesanan berhasil dibatalkan!');
    }

    /**
     * TC-WB-B01 — Branch B4 (Detail Success)
     * Show order detail with correct ownership.
     */
    public function test_show_order_detail_success(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Http::fake([
            'http://localhost:3001/api/history/order/*' => Http::response([
                'success' => true,
                'data' => [
                    'id' => 1,
                    'user_id' => $user->id,
                    'total_harga' => 50000,
                    'status' => 'paid',
                    'items' => [
                         [
                             'nama_produk' => 'Test Product',
                             'qty' => 1,
                             'harga' => 50000
                         ]
                    ]
                ]
            ], 200),
        ]);

        $response = $this->get('/orders/1');

        $response->assertStatus(200);
        $response->assertViewIs('order_detail');
        $response->assertViewHas('order');
    }

    /**
     * TC-WB-B01 — Branch B4a (Forbidden)
     * Detail order for someone else redirects.
     */
    public function test_show_order_detail_forbidden_for_other_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Http::fake([
            'http://localhost:3001/api/history/order/*' => Http::response([
                'success' => true,
                'data' => [
                    'id' => 1,
                    'user_id' => $user->id + 1, // Not owner
                    'total_harga' => 50000,
                    'status' => 'pending',
                    'items' => []
                ]
            ], 200),
        ]);

        $response = $this->get('/orders/1');

        $response->assertRedirect('/riwayat-pesanan');
        $response->assertSessionHas('error', 'Anda tidak memiliki akses ke pesanan ini');
    }
}
