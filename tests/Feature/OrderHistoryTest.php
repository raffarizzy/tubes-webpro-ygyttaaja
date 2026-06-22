<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OrderHistoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * [P1] User belum login, akses GET /api/orders/history
     */
    public function test_p1_unauthorized_user_cannot_access_order_history()
    {
        $controller = new \App\Http\Controllers\OrderController();
        $response = $controller->history();

        $statusCode = $response->getStatusCode();
        $content = json_decode($response->getContent(), true);

        // Menampilkan output hasil test P1 ke terminal
        fwrite(STDERR, "\n========================================\n");
        fwrite(STDERR, " [P1] ACTUAL RESULT OUTPUT\n");
        fwrite(STDERR, "========================================\n");
        fwrite(STDERR, "Status Code   : " . $statusCode . "\n");
        fwrite(STDERR, "JSON Response : " . json_encode($content, JSON_PRETTY_PRINT) . "\n");
        fwrite(STDERR, "========================================\n\n");

        $this->assertEquals(401, $statusCode);
        $this->assertEquals([
            'success' => false,
            'message' => 'Unauthorized',
        ], $content);
    }

    /**
     * [P2] User login, Node.js API aktif, akses GET /api/orders/history
     */
    public function test_p2_authenticated_user_can_fetch_order_history()
    {
        $user = User::factory()->create(['phone' => '08123456789']);

        Http::fake([
            '*/api/history/*' => Http::response([
                'success' => true,
                'data' => [
                    [
                        'id' => 1,
                        'user_id' => $user->id,
                        'totalHarga' => 50000,
                        'status' => 'pending'
                    ]
                ]
            ], 200)
        ]);

        $response = $this->actingAs($user)
                         ->getJson(route('orders.history'));

        // Menampilkan output hasil test P2 ke terminal
        fwrite(STDERR, "\n========================================\n");
        fwrite(STDERR, " [P2] ACTUAL RESULT OUTPUT\n");
        fwrite(STDERR, "========================================\n");
        fwrite(STDERR, "Status Code   : " . $response->status() . "\n");
        fwrite(STDERR, "JSON Response : " . json_encode($response->json(), JSON_PRETTY_PRINT) . "\n");
        fwrite(STDERR, "========================================\n\n");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         [
                             'id' => 1,
                             'user_id' => $user->id,
                             'status' => 'pending'
                         ]
                     ]
                 ]);
    }

    /**
     * [P3] User login, Node.js API gagal/tidak dapat diakses, akses GET /api/orders/history
     */
    public function test_p3_handles_node_api_failure_gracefully()
    {
        $user = User::factory()->create(['phone' => '08123456789']);

        Http::fake([
            '*/api/history/*' => Http::response([
                'success' => false,
                'message' => 'Failed to fetch order history'
            ], 500)
        ]);

        $response = $this->actingAs($user)
                         ->getJson(route('orders.history'));

        // Menampilkan output hasil test P3 ke terminal
        fwrite(STDERR, "\n========================================\n");
        fwrite(STDERR, " [P3] ACTUAL RESULT OUTPUT\n");
        fwrite(STDERR, "========================================\n");
        fwrite(STDERR, "Status Code   : " . $response->status() . "\n");
        fwrite(STDERR, "JSON Response : " . json_encode($response->json(), JSON_PRETTY_PRINT) . "\n");
        fwrite(STDERR, "========================================\n\n");

        $response->assertStatus(500)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Gagal mengambil riwayat pesanan: Failed to fetch order history',
                 ]);
    }
}
