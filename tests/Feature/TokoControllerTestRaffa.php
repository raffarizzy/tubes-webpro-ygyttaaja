<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Client\ConnectionException;
use Tests\TestCase;
use Mockery;

class StoreTokoWhiteBoxTest extends TestCase
{
    use RefreshDatabase;

    private function verifiedUser()
    {
        return User::factory()->create([
            'is_verified_seller' => true,
        ]);
    }

    private function unverifiedUser()
    {
        return User::factory()->create([
            'is_verified_seller' => false,
        ]);
    }

    private function validPayload(): array
    {
        return [
            'nama_toko' => 'Toko Raffa',
            'deskripsi_toko' => 'Toko perlengkapan digital',
            'lokasi' => 'Bandung',
            'provinsi' => 'Jawa Barat',
            'kota' => 'Bandung',
            'kecamatan' => 'Coblong',
            'kode_pos' => '40132',
            'kode_wilayah' => '3273',
            'logo' => UploadedFile::fake()->image('logo.jpg', 600, 600),
        ];
    }

    /**
     * IP1
     * Semua valid, response /check sukses, user belum punya toko,
     * logo tersimpan, POST Node API berhasil.
     *
     * Alur:
     * N1 → N2 → N3 → N4 → N5 → N6 → N7 → N8 → N9 → N10 → N11
     * → N12 → N13 → N14 → N15 → N16 → N17 → N18 → N19 → N20
     * → N21 → N22 → N23 → N24 → N25 → N26 → N28 → N29 → N38
     * → N39 → N37
     */
    public function test_ip1_semua_valid_dan_toko_berhasil_dibuat()
    {
        Storage::fake('public');

        $user = $this->verifiedUser();

        Http::fake(function ($request) {
            if (str_contains($request->url(), '/check')) {
                return Http::response([
                    'data' => [
                        'hasToko' => false,
                    ],
                ], 200);
            }

            return Http::response([
                'data' => [
                    'id' => 1,
                ],
            ], 201);
        });

        $response = $this->actingAs($user)
            ->post(route('toko.store'), $this->validPayload());

        $response->assertRedirect(route('profil_toko'));
        $response->assertSessionHas('success', 'Toko berhasil dibuat!');

        $files = Storage::disk('public')->allFiles('toko');

        $this->assertCount(1, $files);
        $this->assertStringEndsWith('.webp', $files[0]);
    }

    /**
     * IP2
     * User belum verified seller.
     *
     * Alur:
     * N1 → N2 → N42 → N37
     */
    public function test_ip2_user_belum_verified_seller_mendapat_response_403()
    {
        $user = $this->unverifiedUser();

        $response = $this->actingAs($user)
            ->post(route('toko.store'), $this->validPayload());

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'Akun Anda belum terverifikasi sebagai penjual.',
        ]);
    }

    /**
     * IP3
     * Validasi nama_toko gagal.
     *
     * Alur:
     * N1 → N2 → N3 → N41 → N37
     */
    public function test_ip3_validasi_nama_toko_gagal()
    {
        $user = $this->verifiedUser();

        $payload = $this->validPayload();
        $payload['nama_toko'] = str_repeat('A', 51);

        $response = $this->actingAs($user)
            ->from('/buat-toko')
            ->post(route('toko.store'), $payload);

        $response->assertRedirect('/buat-toko');
        $response->assertSessionHasErrors(['nama_toko']);
    }

    /**
     * IP4
     * Validasi deskripsi_toko gagal.
     *
     * Alur:
     * N1 → N2 → N3 → N4 → N41 → N37
     */
    public function test_ip4_validasi_deskripsi_toko_gagal()
    {
        $user = $this->verifiedUser();

        $payload = $this->validPayload();
        $payload['deskripsi_toko'] = str_repeat('A', 101);

        $response = $this->actingAs($user)
            ->from('/buat-toko')
            ->post(route('toko.store'), $payload);

        $response->assertRedirect('/buat-toko');
        $response->assertSessionHasErrors(['deskripsi_toko']);
    }

    /**
     * IP5
     * Validasi lokasi gagal.
     *
     * Alur:
     * N1 → N2 → N3 → N4 → N5 → N41 → N37
     */
    public function test_ip5_validasi_lokasi_gagal()
    {
        $user = $this->verifiedUser();

        $payload = $this->validPayload();
        $payload['lokasi'] = str_repeat('A', 101);

        $response = $this->actingAs($user)
            ->from('/buat-toko')
            ->post(route('toko.store'), $payload);

        $response->assertRedirect('/buat-toko');
        $response->assertSessionHasErrors(['lokasi']);
    }

    /**
     * IP6
     * Validasi provinsi gagal.
     *
     * Alur:
     * N1 → N2 → N3 → N4 → N5 → N6 → N41 → N37
     */
    public function test_ip6_validasi_provinsi_gagal()
    {
        $user = $this->verifiedUser();

        $payload = $this->validPayload();
        $payload['provinsi'] = '';

        $response = $this->actingAs($user)
            ->from('/buat-toko')
            ->post(route('toko.store'), $payload);

        $response->assertRedirect('/buat-toko');
        $response->assertSessionHasErrors(['provinsi']);
    }

    /**
     * IP7
     * Validasi kota gagal.
     *
     * Alur:
     * N1 → N2 → N3 → N4 → N5 → N6 → N7 → N41 → N37
     */
    public function test_ip7_validasi_kota_gagal()
    {
        $user = $this->verifiedUser();

        $payload = $this->validPayload();
        $payload['kota'] = '';

        $response = $this->actingAs($user)
            ->from('/buat-toko')
            ->post(route('toko.store'), $payload);

        $response->assertRedirect('/buat-toko');
        $response->assertSessionHasErrors(['kota']);
    }

    /**
     * IP8
     * Validasi kecamatan gagal.
     *
     * Alur:
     * N1 → N2 → N3 → N4 → N5 → N6 → N7 → N8 → N41 → N37
     */
    public function test_ip8_validasi_kecamatan_gagal()
    {
        $user = $this->verifiedUser();

        $payload = $this->validPayload();
        $payload['kecamatan'] = '';

        $response = $this->actingAs($user)
            ->from('/buat-toko')
            ->post(route('toko.store'), $payload);

        $response->assertRedirect('/buat-toko');
        $response->assertSessionHasErrors(['kecamatan']);
    }

    /**
     * IP9
     * Validasi kode_pos gagal.
     *
     * Alur:
     * N1 → N2 → N3 → N4 → N5 → N6 → N7 → N8 → N9 → N41 → N37
     */
    public function test_ip9_validasi_kode_pos_gagal()
    {
        $user = $this->verifiedUser();

        $payload = $this->validPayload();
        $payload['kode_pos'] = '123456';

        $response = $this->actingAs($user)
            ->from('/buat-toko')
            ->post(route('toko.store'), $payload);

        $response->assertRedirect('/buat-toko');
        $response->assertSessionHasErrors(['kode_pos']);
    }

    /**
     * IP10
     * Validasi kode_wilayah gagal.
     *
     * Alur:
     * N1 → N2 → N3 → N4 → N5 → N6 → N7 → N8 → N9 → N10 → N41 → N37
     */
    public function test_ip10_validasi_kode_wilayah_gagal()
    {
        $user = $this->verifiedUser();

        $payload = $this->validPayload();
        $payload['kode_wilayah'] = '';

        $response = $this->actingAs($user)
            ->from('/buat-toko')
            ->post(route('toko.store'), $payload);

        $response->assertRedirect('/buat-toko');
        $response->assertSessionHasErrors(['kode_wilayah']);
    }

    /**
     * IP11
     * Validasi logo gagal.
     *
     * Alur:
     * N1 → N2 → N3 → N4 → N5 → N6 → N7 → N8 → N9 → N10 → N11
     * → N41 → N37
     */
    public function test_ip11_validasi_logo_gagal()
    {
        $user = $this->verifiedUser();

        $payload = $this->validPayload();
        $payload['logo'] = UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf');

        $response = $this->actingAs($user)
            ->from('/buat-toko')
            ->post(route('toko.store'), $payload);

        $response->assertRedirect('/buat-toko');
        $response->assertSessionHasErrors(['logo']);
    }

    /**
     * IP12
     * Terjadi exception pada blok try, masuk catch,
     * file belum ada di storage.
     *
     * Alur:
     * N1 → N2 → N3 → N4 → N5 → N6 → N7 → N8 → N9 → N10 → N11
     * → N12 → N32 → N33 → N34 → N36 → N37
     */
    public function test_ip12_exception_di_try_sebelum_file_dibuat()
    {
        Storage::fake('public');

        $user = $this->verifiedUser();

        Http::fake(function ($request) {
            if (str_contains($request->url(), '/check')) {
                throw new ConnectionException('Koneksi ke API gagal');
            }

            return Http::response([], 500);
        });

        $response = $this->actingAs($user)
            ->from('/buat-toko')
            ->post(route('toko.store'), $this->validPayload());

        $response->assertRedirect('/buat-toko');
        $response->assertSessionHas('error');

        $this->assertCount(0, Storage::disk('public')->allFiles('toko'));
    }

    /**
     * IP13
     * Response API /check tidak sukses,
     * tetapi proses tetap lanjut membuat toko.
     *
     * Alur:
     * N1 → N2 → N3 → N4 → N5 → N6 → N7 → N8 → N9 → N10 → N11
     * → N12 → N13 → N14 → N16 → N17 → N18 → N19 → N20 → N21
     * → N22 → N23 → N24 → N25 → N26 → N28 → N29 → N38 → N39 → N37
     */
    public function test_ip13_response_check_tidak_sukses_tetap_lanjut_membuat_toko()
    {
        Storage::fake('public');

        $user = $this->verifiedUser();

        Http::fake(function ($request) {
            if (str_contains($request->url(), '/check')) {
                return Http::response([
                    'message' => 'API check error',
                ], 500);
            }

            return Http::response([
                'data' => [
                    'id' => 1,
                ],
            ], 201);
        });

        $response = $this->actingAs($user)
            ->post(route('toko.store'), $this->validPayload());

        $response->assertRedirect(route('profil_toko'));
        $response->assertSessionHas('success', 'Toko berhasil dibuat!');

        $this->assertCount(1, Storage::disk('public')->allFiles('toko'));
    }

    /**
     * IP14
     * Response API /check sukses dan user sudah memiliki toko.
     *
     * Alur:
     * N1 → N2 → N3 → N4 → N5 → N6 → N7 → N8 → N9 → N10 → N11
     * → N12 → N13 → N14 → N15 → N40 → N37
     */
    public function test_ip14_user_sudah_memiliki_toko()
    {
        Storage::fake('public');

        $user = $this->verifiedUser();

        Http::fake(function ($request) {
            if (str_contains($request->url(), '/check')) {
                return Http::response([
                    'data' => [
                        'hasToko' => true,
                    ],
                ], 200);
            }

            return Http::response([], 500);
        });

        $response = $this->actingAs($user)
            ->from('/buat-toko')
            ->post(route('toko.store'), $this->validPayload());

        $response->assertRedirect('/buat-toko');
        $response->assertSessionHas('error', 'Anda sudah memiliki toko');

        $this->assertCount(0, Storage::disk('public')->allFiles('toko'));
    }

    /**
     * IP15
     * Logo gagal tersimpan, masuk log gagal,
     * tetapi proses tetap lanjut sampai berhasil.
     *
     * Alur:
     * N1 → N2 → N3 → N4 → N5 → N6 → N7 → N8 → N9 → N10 → N11
     * → N12 → N13 → N14 → N15 → N16 → N17 → N18 → N19 → N20
     * → N21 → N22 → N23 → N24 → N27 → N26 → N28 → N29 → N38
     * → N39 → N37
     */
    public function test_ip15_logo_gagal_tersimpan_tetapi_proses_tetap_lanjut()
    {
        $user = $this->verifiedUser();

        Http::fake(function ($request) {
            if (str_contains($request->url(), '/check')) {
                return Http::response([
                    'data' => [
                        'hasToko' => false,
                    ],
                ], 200);
            }

            return Http::response([
                'data' => [
                    'id' => 1,
                ],
            ], 201);
        });

        Log::shouldReceive('error')
            ->atLeast()
            ->once();

        $disk = Mockery::mock(\Illuminate\Contracts\Filesystem\Filesystem::class);

        $disk->shouldReceive('put')
            ->once()
            ->andReturn(false);

        $disk->shouldReceive('url')
            ->zeroOrMoreTimes()
            ->andReturn('/storage/toko/fake.webp');

        Storage::shouldReceive('disk')
            ->with('public')
            ->andReturn($disk);

        $response = $this->actingAs($user)
            ->post(route('toko.store'), $this->validPayload());

        $response->assertRedirect(route('profil_toko'));
        $response->assertSessionHas('success', 'Toko berhasil dibuat!');
    }

    /**
     * IP16
     * POST ke Node API gagal, logo dihapus,
     * masuk catch, file ada di storage.
     *
     * Alur:
     * N1 → N2 → N3 → N4 → N5 → N6 → N7 → N8 → N9 → N10 → N11
     * → N12 → N13 → N14 → N15 → N16 → N17 → N18 → N19 → N20
     * → N21 → N22 → N23 → N24 → N25 → N26 → N28 → N29 → N30
     * → N31 → N32 → N33 → N34 → N35 → N36 → N37
     */
    public function test_ip16_post_node_api_gagal_logo_dihapus_dan_redirect_error()
    {
        Storage::fake('public');

        $user = $this->verifiedUser();

        Http::fake(function ($request) {
            if (str_contains($request->url(), '/check')) {
                return Http::response([
                    'data' => [
                        'hasToko' => false,
                    ],
                ], 200);
            }

            return Http::response([
                'message' => 'Gagal menyimpan toko ke API',
            ], 500);
        });

        $response = $this->actingAs($user)
            ->from('/buat-toko')
            ->post(route('toko.store'), $this->validPayload());

        $response->assertRedirect('/buat-toko');
        $response->assertSessionHas('error');

        $this->assertCount(0, Storage::disk('public')->allFiles('toko'));
    }

    /**
     * IP17
     * Terjadi exception pada blok try,
     * file sudah ada di storage dan dihapus.
     *
     * Alur:
     * N1 → N2 → N3 → N4 → N5 → N6 → N7 → N8 → N9 → N10 → N11
     * → N12 → N32 → N33 → N34 → N35 → N36 → N37
     */
    public function test_ip17_exception_setelah_logo_path_ada_file_dihapus()
    {
        Storage::fake('public');

        $user = $this->verifiedUser();

        Http::fake(function ($request) {
            if (str_contains($request->url(), '/check')) {
                return Http::response([
                    'data' => [
                        'hasToko' => false,
                    ],
                ], 200);
            }

            /*
             * Response status 201 berarti lolos dari pengecekan status gagal.
             * Tetapi struktur response tidak memiliki data.id,
             * sehingga baris:
             * $nodeTokoId = $response->json()['data']['id'];
             * akan memicu exception dan masuk catch.
             */
            return Http::response([], 201);
        });

        $response = $this->actingAs($user)
            ->from('/buat-toko')
            ->post(route('toko.store'), $this->validPayload());

        $response->assertRedirect('/buat-toko');
        $response->assertSessionHas('error');

        $this->assertCount(0, Storage::disk('public')->allFiles('toko'));
    }
}