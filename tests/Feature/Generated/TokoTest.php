<?php

namespace Tests\Feature\Generated;

use App\Models\User;
use App\Models\Toko;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class TokoTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    /**
     * TC-WB-RA02 — Branch B1
     * User has no store, redirected to create.
     */
    public function test_index_redirects_to_create_if_no_toko(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Mock Node.js API check: hasToko = false
        Http::fake([
            'http://localhost:3001/api/toko/check' => Http::response([
                'data' => ['hasToko' => false]
            ], 200),
        ]);

        $response = $this->get('/toko');

        $response->assertRedirect('/toko/create');
    }

    /**
     * TC-WB-RA02 — Branch B2, B3
     * User has store, shows profile.
     */
    public function test_index_shows_profile_if_toko_exists(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Mock Node.js API check: hasToko = true
        // Mock Node.js API my-toko
        Http::fake([
            'http://localhost:3001/api/toko/check' => Http::response([
                'data' => ['hasToko' => true]
            ], 200),
            'http://localhost:3001/api/toko/my-toko' => Http::response([
                'data' => [
                    'id' => 1,
                    'nama_toko' => 'Toko Raffa',
                    'deskripsi_toko' => 'Deskripsi Toko',
                    'lokasi' => 'Bandung',
                    'logo_path' => 'toko/logo.png'
                ]
            ], 200),
        ]);

        $this->withoutExceptionHandling();
        $response = $this->get('/toko');

        $response->assertStatus(200);
        $response->assertViewIs('profil_toko');
    }

    /**
     * TC-WB-RA02 — Branch B5, B7
     * API timeout, fallback to Eloquent.
     */
    public function test_index_fallback_to_eloquent_if_api_fails(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Seed Eloquent
        Toko::create([
            'user_id' => $user->id,
            'nama_toko' => 'Toko Fallback',
            'deskripsi_toko' => 'Fallback Desk',
            'lokasi' => 'Jakarta',
            'logo_path' => 'toko/fallback.png'
        ]);

        // Mock API failure
        Http::fake([
            'http://localhost:3001/api/toko/check' => Http::response([], 500),
        ]);

        $this->withoutExceptionHandling();
        $response = $this->get('/toko');

        $response->assertStatus(200);
        $response->assertViewIs('profil_toko');
        $response->assertViewHas('toko');
    }

    /**
     * TC-WB-RA02 — Branch B9, B11, B14
     * Store creation success.
     */
    public function test_store_creation_success(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->actingAs($user);

        Http::fake([
            'http://localhost:3001/api/toko/check' => Http::response([
                'data' => ['hasToko' => false]
            ], 200),
            'http://localhost:3001/api/toko' => Http::response([
                'success' => true
            ], 201),
        ]);

        $logo = UploadedFile::fake()->image('logo.jpg');

        $response = $this->post('/toko', [
            'nama_toko' => 'Toko Raffa',
            'deskripsi_toko' => 'Deskripsi Toko',
            'lokasi' => 'Bandung',
            'logo' => $logo
        ]);

        $response->assertRedirect('/toko');
        $response->assertSessionHas('success', 'Toko berhasil dibuat!');
        
        // Check file exists in storage
        Storage::disk('public')->assertExists('toko/' . $logo->hashName());
    }

    /**
     * TC-WB-RA02 — Branch B8
     * Store creation validation fail.
     */
    public function test_store_validation_fails_if_nama_is_empty(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/toko', [
            'nama_toko' => '',
            'deskripsi_toko' => 'Desk',
            'lokasi' => 'Loc',
            'logo' => UploadedFile::fake()->image('logo.jpg')
        ]);

        $response->assertSessionHasErrors(['nama_toko']);
    }

    /**
     * TC-WB-RA02 — Branch B17
     * Update access denied (forbidden).
     */
    public function test_update_denied_if_not_owner(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        
        $toko = Toko::create([
            'user_id' => $owner->id,
            'nama_toko' => 'Owner Toko',
            'deskripsi_toko' => 'Desc',
            'lokasi' => 'Loc',
            'logo_path' => 'path.png'
        ]);

        $this->actingAs($otherUser);

        $response = $this->put("/toko/{$toko->id}", [
            'nama_toko' => 'Hacked Name',
            'deskripsi_toko' => 'Hacked Desc',
            'lokasi' => 'Hacked Loc'
        ]);

        $response->assertStatus(403);
    }
}
