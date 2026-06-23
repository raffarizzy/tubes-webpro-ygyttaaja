<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * TC-WB-N02 — CheckoutController::pay() Pembayaran Duitku (Branch Coverage) — FR7
 *
 * Peta cabang pay() (app/Http/Controllers/CheckoutController.php b.108–195):
 *   B1  (b.110) !Auth::check()                  -> 401 Unauthorized
 *   B2  (b.114) validasi order_id/total gagal   -> 422
 *   B3  (b.128) !merchantCode || !apiKey        -> 500 "Konfigurasi Duitku belum lengkap di .env"
 *   B4  (b.140) mode === 'production' ? .. : .. -> pilih URL prod / sandbox (ternary)
 *   B5  (b.163) !$response->successful()        -> 500 "Gagal menghubungi server Duitku"
 *   B6  (b.170) statusCode !== '00'             -> 500 "Duitku: <pesan>"
 *   B7  (b.185) semua lolos                     -> 200 { success:true, payment_url, reference }
 *   catch (b.191) menangkap throw B3/B5/B6      -> 500
 *
 * V(G) ≈ 7. Node API & Duitku di-fake dengan Http::fake()
 */

class CheckoutPayWhiteBoxTest extends TestCase
{
    use RefreshDatabase;

    private const URL_SANDBOX = 'https://api-sandbox.duitku.com/*';
    private const URL_PROD    = 'https://api.duitku.com/*';
    private const URL_NODE    = 'http://localhost:3001/*';

    private function makeUser(): User
    {
        return User::forceCreate([
            'name'              => 'Buyer WB',
            'email'             => 'buyer_' . uniqid() . '@test.com',
            'password'          => bcrypt('password'),
            'phone'             => '0812' . random_int(1000000, 9999999),
            'email_verified_at' => now(),
        ]);
    }

    /** Set kredensial Duitku agar lolos cek B3. */
    private function setDuitkuConfig(string $mode = 'sandbox'): void
    {
        config()->set('services.duitku.merchant_code', 'DXXXX');
        config()->set('services.duitku.api_key', 'apikey-123');
        config()->set('services.duitku.mode', $mode);
    }

    /** B1 — tidak login -> 401 (middleware dilewati agar sampai ke controller). */
    public function test_b1_tidak_login_unauthorized(): void
    {
        $this->withoutMiddleware();

        $response = $this->postJson(route('checkout.pay'), ['order_id' => 1, 'total' => 50000]);

        $response->assertStatus(401);
        $response->assertJson(['success' => false]);
    }

    /** B2 — validasi gagal (order_id kosong) -> 422. */
    public function test_b2_validasi_gagal_422(): void
    {
        $this->actingAs($this->makeUser());

        $response = $this->postJson(route('checkout.pay'), ['total' => 50000]); // tanpa order_id

        $response->assertStatus(422);
    }

    /** B3 — konfigurasi Duitku kosong -> 500. */
    public function test_b3_konfigurasi_duitku_kosong_500(): void
    {
        $this->actingAs($this->makeUser());
        config()->set('services.duitku.merchant_code', null);
        config()->set('services.duitku.api_key', null);

        $response = $this->postJson(route('checkout.pay'), ['order_id' => 1, 'total' => 50000]);

        $response->assertStatus(500);
        $response->assertJsonFragment(['message' => 'Konfigurasi Duitku belum lengkap di .env']);
    }

    /** B5 — Duitku membalas non-2xx -> 500 "Gagal menghubungi server Duitku". */
    public function test_b5_duitku_gagal_dihubungi_500(): void
    {
        $this->actingAs($this->makeUser());
        $this->setDuitkuConfig();
        Http::fake([self::URL_SANDBOX => Http::response('error', 500)]);

        $response = $this->postJson(route('checkout.pay'), ['order_id' => 1, 'total' => 50000]);

        $response->assertStatus(500);
        $response->assertJsonFragment(['message' => 'Gagal menghubungi server Duitku']);
    }

    /** B6 — Duitku 200 tapi statusCode != '00' -> 500 "Duitku: ...". */
    public function test_b6_duitku_status_code_bukan_00_500(): void
    {
        $this->actingAs($this->makeUser());
        $this->setDuitkuConfig();
        Http::fake([
            self::URL_SANDBOX => Http::response(['statusCode' => '01', 'statusMessage' => 'Transaksi ditolak'], 200),
        ]);

        $response = $this->postJson(route('checkout.pay'), ['order_id' => 1, 'total' => 50000]);

        $response->assertStatus(500);
        $response->assertJsonFragment(['message' => 'Duitku: Transaksi ditolak']);
    }

    /** B7 — semua lolos (sandbox) -> 200 { success, payment_url, reference }. */
    public function test_b7_sukses_sandbox_200(): void
    {
        $this->actingAs($this->makeUser());
        $this->setDuitkuConfig('sandbox');
        Http::fake([
            self::URL_SANDBOX => Http::response([
                'statusCode' => '00',
                'paymentUrl' => 'https://sandbox.duitku.com/pay/INV123',
                'reference'  => 'REF-123',
            ], 200),
            self::URL_NODE => Http::response(['success' => true], 200), // sinkronisasi status ke Node
        ]);

        $response = $this->postJson(route('checkout.pay'), ['order_id' => 1, 'total' => 50000]);

        $response->assertOk();
        $response->assertJson([
            'success'     => true,
            'payment_url' => 'https://sandbox.duitku.com/pay/INV123',
            'reference'   => 'REF-123',
        ]);
    }

    /** B4 — mode production: pakai URL produksi (cabang ternary TRUE) -> 200. */
    public function test_b4_mode_production_pakai_url_produksi(): void
    {
        $this->actingAs($this->makeUser());
        $this->setDuitkuConfig('production');
        Http::fake([
            self::URL_PROD => Http::response([
                'statusCode' => '00',
                'paymentUrl' => 'https://app.duitku.com/pay/INV999',
                'reference'  => 'REF-999',
            ], 200),
            self::URL_NODE => Http::response(['success' => true], 200),
        ]);

        $response = $this->postJson(route('checkout.pay'), ['order_id' => 1, 'total' => 50000]);

        $response->assertOk();
        $response->assertJsonFragment(['reference' => 'REF-999']);
        // pastikan benar-benar memanggil endpoint PRODUKSI (bukan sandbox)
        Http::assertSent(fn ($request) => str_contains($request->url(), 'api.duitku.com'));
    }
}
