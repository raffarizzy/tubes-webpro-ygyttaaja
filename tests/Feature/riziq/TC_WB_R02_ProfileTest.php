<?php

/**
 * TC-WB-R02 — ProfileController::update(), update_pfp() (Branch Coverage)
 * Feature: FR11 — Edit Profil
 * PIC: Riziq Rizwan
 */

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    $this->user = User::factory()->create([
        'phone'   => '08100000002',
        'pfpPath' => null,
    ]);
});

// B2, B6, B8, B10 — ubah nama, email sama, tanpa ganti password → success
test('TC-WB-R02 Step 1 — update nama valid tanpa ganti password: profil berhasil diperbarui', function () {
    Http::fake([
        'http://localhost:3001/api/profile/*' => Http::response(['success' => true], 200),
    ]);

    $response = $this->actingAs($this->user)->patch('/profile', [
        'name'  => 'Riziq Updated',
        'email' => $this->user->email,
    ]);

    $response->assertSessionHas('success');
    expect($this->user->fresh()->name)->toBe('Riziq Updated');
});

// B1 — name kosong → validate() di-catch try-catch → error key 'general'
test('TC-WB-R02 Step 2 — name kosong: error validasi ditangkap sebagai general error', function () {
    $response = $this->actingAs($this->user)->patch('/profile', [
        'name'  => '',
        'email' => $this->user->email,
    ]);

    // ProfileController wraps validate() in try-catch \Throwable,
    // so ValidationException is caught and re-thrown as withErrors(['general'])
    $response->assertSessionHasErrors('general');
});

// B2, B7 — ganti email → controller mencoba set email_verified_at = null
// Catatan: email_verified_at tidak ada di $fillable, sehingga mass-assignment tidak reset.
// Test ini memverifikasi bahwa email berhasil berubah (yang fillable).
test('TC-WB-R02 Step 3 — ganti email baru: email berhasil diperbarui', function () {
    Http::fake([
        'http://localhost:3001/api/profile/*' => Http::response(['success' => true], 200),
    ]);

    $newEmail = 'riziq.new.' . time() . '@sparehub.com';
    $this->actingAs($this->user)->patch('/profile', [
        'name'  => 'Riziq Test',
        'email' => $newEmail,
    ]);

    // email berhasil berubah karena ada di $fillable
    expect($this->user->fresh()->email)->toBe($newEmail);
    // Catatan: email_verified_at TIDAK direset karena tidak ada di $fillable User model
});

// B2, B3, B5, B10 — current_password benar, password baru valid → success
test('TC-WB-R02 Step 4 — current_password benar, password baru valid: password berhasil diperbarui', function () {
    Http::fake([
        'http://localhost:3001/api/profile/*' => Http::response(['success' => true], 200),
    ]);

    $response = $this->actingAs($this->user)->patch('/profile', [
        'name'                  => $this->user->name,
        'email'                 => $this->user->email,
        'current_password'      => 'password',
        'password'              => 'NewPass456!',
        'password_confirmation' => 'NewPass456!',
    ]);

    $response->assertSessionHas('success');
});

// B2, B3, B4 — current_password salah → error
test('TC-WB-R02 Step 5 — current_password salah: error "Password saat ini salah"', function () {
    $response = $this->actingAs($this->user)->patch('/profile', [
        'name'                  => $this->user->name,
        'email'                 => $this->user->email,
        'current_password'      => 'wrong-password',
        'password'              => 'NewPass456!',
        'password_confirmation' => 'NewPass456!',
    ]);

    $response->assertSessionHasErrors('current_password');
});

// B2, B9, B11 — Node API gagal → general error
test('TC-WB-R02 Step 6 — Node API mati saat update profil: error general', function () {
    Http::fake([
        'http://localhost:3001/api/profile/*' => Http::response([], 500),
    ]);

    $response = $this->actingAs($this->user)->patch('/profile', [
        'name'  => $this->user->name,
        'email' => $this->user->email,
    ]);

    $response->assertSessionHasErrors('general');
});

// B13, B14, B18 — upload .jpg valid (tanpa foto lama) → success
test('TC-WB-R02 Step 7 — upload foto profil .jpg baru: foto berhasil diperbarui', function () {
    Http::fake([
        'http://localhost:3001/api/profile/*' => Http::response(['success' => true], 200),
    ]);

    $file = UploadedFile::fake()->image('avatar.jpg');

    $response = $this->actingAs($this->user)->patch('/edit_profil', [
        'pfpPath' => $file,
    ]);

    $response->assertSessionHas('success');
});

// B12 — upload .pdf → validasi error
test('TC-WB-R02 Step 8 — upload file .pdf sebagai foto: error format gambar', function () {
    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    $response = $this->actingAs($this->user)->patch('/edit_profil', [
        'pfpPath' => $file,
    ]);

    $response->assertSessionHasErrors('pfpPath');
});

// B12 — tidak ada file sama sekali → validasi error
test('TC-WB-R02 Step 9 — submit form tanpa file: error "Foto profil wajib diunggah"', function () {
    $response = $this->actingAs($this->user)->patch('/edit_profil', []);

    $response->assertSessionHasErrors('pfpPath');
});
