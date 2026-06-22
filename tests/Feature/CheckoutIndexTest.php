<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CheckoutIndexTest extends TestCase
{
    use RefreshDatabase;

    /**
     * [P1] Jalur Alamat Error: User login, Node.js API aktif, Server Alamat down (500/404), Server Cart up (200)
     */
    public function test_p1_alamat_error_path()
    {
        $user = User::factory()->create(['phone' => '08123456789']);

        // Mock: Alamat API returns 500, Cart API returns 200 with items
        Http::fake([
            '*/api/alamat/*' => Http::response('Server Error', 500),
            '*/api/cart/*' => Http::response([
                'success' => true,
                'data' => [
                    'items' => [
                        [
                            'id' => 1,
                            'product_id' => 10,
                            'jumlah' => 2,
                            'subtotal' => 100000,
                            'product' => [
                                'id' => 10,
                                'nama' => 'Produk A',
                                'harga' => 50000,
                                'imagePath' => 'produk_a.png',
                                'deskripsi' => 'Deskripsi Produk A'
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->actingAs($user)->get(route('checkout'));

        $response->assertStatus(200);
        $response->assertViewIs('checkout');

        $view = $response->original;
        $viewData = $view->getData();
        $alamats = $viewData['alamats'] ?? collect([]);
        $cartItems = $viewData['cartItems'] ?? collect([]);
        $subtotal = $viewData['subtotal'] ?? 0;
        $total = $viewData['total'] ?? 0;

        // Print output to terminal
        fwrite(STDERR, "\n========================================\n");
        fwrite(STDERR, " [P1] ACTUAL RESULT OUTPUT (Alamat Error)\n");
        fwrite(STDERR, "========================================\n");
        fwrite(STDERR, "View Name        : " . $view->name() . "\n");
        fwrite(STDERR, "Alamats Count    : " . count($alamats) . "\n");
        fwrite(STDERR, "Cart Items Count : " . count($cartItems) . "\n");
        fwrite(STDERR, "Subtotal         : Rp " . number_format($subtotal, 0, ',', '.') . "\n");
        fwrite(STDERR, "Total            : Rp " . number_format($total, 0, ',', '.') . "\n");
        fwrite(STDERR, "========================================\n\n");

        $this->assertCount(0, $alamats);
        $this->assertCount(1, $cartItems);
        $this->assertEquals(100000, $subtotal);
        $this->assertEquals(100000, $total);
    }

    /**
     * [P2] Jalur Belum Login: User tidak login, Aplikasi web aktif
     */
    public function test_p2_unauthorized_redirects_to_login()
    {
        // Memanggil method controller secara langsung untuk menguji logika internal controller
        // tanpa terhadang oleh middleware 'auth' dari routing Laravel.
        $controller = new \App\Http\Controllers\CheckoutController();
        $response = $controller->index();

        $redirectTarget = $response->headers->get('Location');
        $sessionError = $response->getSession()->get('error');

        // Print output to terminal
        fwrite(STDERR, "\n========================================\n");
        fwrite(STDERR, " [P2] ACTUAL RESULT OUTPUT (Belum Login)\n");
        fwrite(STDERR, "========================================\n");
        fwrite(STDERR, "Status Code      : " . $response->getStatusCode() . "\n");
        fwrite(STDERR, "Redirect Target  : " . $redirectTarget . "\n");
        fwrite(STDERR, "Session Error    : " . $sessionError . "\n");
        fwrite(STDERR, "========================================\n\n");

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertEquals(route('login'), $redirectTarget);
        $this->assertEquals('Silakan login terlebih dahulu', $sessionError);
    }

    /**
     * [P3] Jalur Sukses Utama: User login, Node.js API aktif, Server Alamat up (200), Server Cart up (200)
     */
    public function test_p3_success_path()
    {
        $user = User::factory()->create(['phone' => '08123456789']);

        // Mock: Alamat API returns 200, Cart API returns 200
        Http::fake([
            '*/api/alamat/*' => Http::response([
                'success' => true,
                'data' => [
                    [
                        'id' => 5,
                        'nama_penerima' => 'Bagas Pratama',
                        'phone' => '08123456789',
                        'alamat_lengkap' => 'Jl. Tebet No. 12'
                    ]
                ]
            ], 200),
            '*/api/cart/*' => Http::response([
                'success' => true,
                'data' => [
                    'items' => [
                        [
                            'id' => 1,
                            'product_id' => 10,
                            'jumlah' => 2,
                            'subtotal' => 100000,
                            'product' => [
                                'id' => 10,
                                'nama' => 'Produk A',
                                'harga' => 50000,
                                'imagePath' => 'produk_a.png',
                                'deskripsi' => 'Deskripsi Produk A'
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->actingAs($user)->get(route('checkout'));

        $response->assertStatus(200);
        $response->assertViewIs('checkout');

        $view = $response->original;
        $viewData = $view->getData();
        $alamats = $viewData['alamats'] ?? collect([]);
        $cartItems = $viewData['cartItems'] ?? collect([]);
        $subtotal = $viewData['subtotal'] ?? 0;
        $total = $viewData['total'] ?? 0;

        // Print output to terminal
        fwrite(STDERR, "\n========================================\n");
        fwrite(STDERR, " [P3] ACTUAL RESULT OUTPUT (Sukses Utama)\n");
        fwrite(STDERR, "========================================\n");
        fwrite(STDERR, "View Name        : " . $view->name() . "\n");
        fwrite(STDERR, "Alamats Count    : " . count($alamats) . "\n");
        fwrite(STDERR, "Cart Items Count : " . count($cartItems) . "\n");
        fwrite(STDERR, "Subtotal         : Rp " . number_format($subtotal, 0, ',', '.') . "\n");
        fwrite(STDERR, "Total            : Rp " . number_format($total, 0, ',', '.') . "\n");
        fwrite(STDERR, "========================================\n\n");

        $this->assertCount(1, $alamats);
        $this->assertCount(1, $cartItems);
        $this->assertEquals(100000, $subtotal);
        $this->assertEquals(100000, $total);
    }

    /**
     * [P4] Jalur Cart Error: User login, Node.js API aktif, Server Alamat up (200), Server Cart down/timeout (500)
     */
    public function test_p4_cart_error_path()
    {
        $user = User::factory()->create(['phone' => '08123456789']);

        // Mock: Alamat API returns 200, Cart API returns 500
        Http::fake([
            '*/api/alamat/*' => Http::response([
                'success' => true,
                'data' => [
                    [
                        'id' => 5,
                        'nama_penerima' => 'Bagas Pratama',
                        'phone' => '08123456789',
                        'alamat_lengkap' => 'Jl. Tebet No. 12'
                    ]
                ]
            ], 200),
            '*/api/cart/*' => Http::response('Cart Server Error', 500)
        ]);

        $response = $this->actingAs($user)->get(route('checkout'));

        $response->assertStatus(200);
        $response->assertViewIs('checkout');

        $view = $response->original;
        $viewData = $view->getData();
        $alamats = $viewData['alamats'] ?? collect([]);
        $cartItems = $viewData['cartItems'] ?? collect([]);
        $subtotal = $viewData['subtotal'] ?? 0;
        $total = $viewData['total'] ?? 0;
        $error = $viewData['error'] ?? '';

        // Print output to terminal
        fwrite(STDERR, "\n========================================\n");
        fwrite(STDERR, " [P4] ACTUAL RESULT OUTPUT (Cart Error)\n");
        fwrite(STDERR, "========================================\n");
        fwrite(STDERR, "View Name        : " . $view->name() . "\n");
        fwrite(STDERR, "Alamats Count    : " . count($alamats) . "\n");
        fwrite(STDERR, "Cart Items Count : " . count($cartItems) . "\n");
        fwrite(STDERR, "Subtotal         : Rp " . number_format($subtotal, 0, ',', '.') . "\n");
        fwrite(STDERR, "Total            : Rp " . number_format($total, 0, ',', '.') . "\n");
        fwrite(STDERR, "Error Variable   : " . $error . "\n");
        fwrite(STDERR, "========================================\n\n");

        $this->assertCount(1, $alamats);
        $this->assertCount(0, $cartItems);
        $this->assertEquals(0, $subtotal);
        $this->assertEquals(0, $total);
        $this->assertEquals('Gagal memuat data keranjang', $error);
    }

    /**
     * [P5] Jalur Exception Handle: data Cart rusak/tidak sesuai memicu Exception
     */
    public function test_p5_exception_path()
    {
        $user = User::factory()->create(['phone' => '08123456789']);

        // Mock: Alamat API returns 200, Cart API returns data without 'product' key which triggers PHP notice/exception when accessed in Controller
        Http::fake([
            '*/api/alamat/*' => Http::response([
                'success' => true,
                'data' => [
                    [
                        'id' => 5,
                        'nama_penerima' => 'Bagas Pratama',
                        'phone' => '08123456789',
                        'alamat_lengkap' => 'Jl. Tebet No. 12'
                    ]
                ]
            ], 200),
            '*/api/cart/*' => Http::response([
                'success' => true,
                'data' => [
                    'items' => [
                        [
                            'id' => 1,
                            'product_id' => 10,
                            'jumlah' => 2,
                            'subtotal' => 100000
                            // 'product' is intentionally missing to trigger TypeError/Exception in the array map
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->actingAs($user)->get(route('checkout'));

        $response->assertStatus(200);
        $response->assertViewIs('checkout');

        $view = $response->original;
        $viewData = $view->getData();
        $alamats = $viewData['alamats'] ?? collect([]);
        $cartItems = $viewData['cartItems'] ?? collect([]);
        $subtotal = $viewData['subtotal'] ?? 0;
        $total = $viewData['total'] ?? 0;
        $error = $viewData['error'] ?? '';

        // Print output to terminal
        fwrite(STDERR, "\n========================================\n");
        fwrite(STDERR, " [P5] ACTUAL RESULT OUTPUT (Exception)\n");
        fwrite(STDERR, "========================================\n");
        fwrite(STDERR, "View Name        : " . $view->name() . "\n");
        fwrite(STDERR, "Alamats Count    : " . count($alamats) . "\n");
        fwrite(STDERR, "Cart Items Count : " . count($cartItems) . "\n");
        fwrite(STDERR, "Subtotal         : Rp " . number_format($subtotal, 0, ',', '.') . "\n");
        fwrite(STDERR, "Total            : Rp " . number_format($total, 0, ',', '.') . "\n");
        fwrite(STDERR, "Error Variable   : " . $error . "\n");
        fwrite(STDERR, "========================================\n\n");

        $this->assertCount(0, $alamats);
        $this->assertCount(0, $cartItems);
        $this->assertEquals(0, $subtotal);
        $this->assertEquals(0, $total);
        $this->assertStringContainsString('Gagal memuat halaman checkout:', $error);
    }
}
