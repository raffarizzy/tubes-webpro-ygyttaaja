<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| TC-WB-R02 — ProfileController::update(), update_pfp() (Branch Coverage)
|--------------------------------------------------------------------------
*/

test('TC-WB-R02 Step 1 (B2, B6, B8, B10): Login, buka /edit_profil, ubah nama menjadi Riziq Updated, email sama, tidak ganti password, klik Simpan', function () {
    Http::fake([
        config('services.node_api.url') . '/api/profile/*' => Http::response(['success' => true], 200),
    ]);

    $user = User::factory()->create([
        'name' => 'Original Name',
        'email' => 'riziq@sparehub.com',
    ]);

    $response = $this->actingAs($user)->patch('/profile', [
        'name' => 'Riziq Updated',
        'email' => 'riziq@sparehub.com',
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertSessionHas('success', 'Profil berhasil diperbarui');
    expect($user->refresh()->name)->toBe('Riziq Updated');
    expect($user->email)->toBe('riziq@sparehub.com');
});

test('TC-WB-R02 Step 2 (B1): Hapus isi field name, klik Simpan', function () {
    $user = User::factory()->create([
        'name' => 'Original Name',
        'email' => 'riziq@sparehub.com',
    ]);

    $response = $this->actingAs($user)->patch('/profile', [
        'name' => '',
        'email' => 'riziq@sparehub.com',
    ]);

    $response->assertSessionHasErrors('general');
    $errors = session('errors')->get('general');
    expect($errors[0])->toContain('Username wajib diisi.');
});

test('TC-WB-R02 Step 3 (B2, B7, B10): Ganti email ke riziq.new@sparehub.com (belum dipakai), klik Simpan', function () {
    Http::fake([
        config('services.node_api.url') . '/api/profile/*' => Http::response(['success' => true], 200),
    ]);

    $user = User::factory()->create([
        'name' => 'Riziq',
        'email' => 'riziq@sparehub.com',
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($user)->patch('/profile', [
        'name' => 'Riziq',
        'email' => 'riziq.new@sparehub.com',
    ]);

    $response->assertSessionHasNoErrors();
    $user->refresh();
    expect($user->email)->toBe('riziq.new@sparehub.com');
    expect($user->email_verified_at)->toBeNull();
});

test('TC-WB-R02 Step 4 (B2, B3, B5, B10): Isi current_password benar, password baru valid & cocok, klik Simpan', function () {
    Http::fake([
        config('services.node_api.url') . '/api/profile/*' => Http::response(['success' => true], 200),
    ]);

    $user = User::factory()->create([
        'name' => 'Riziq',
        'email' => 'riziq@sparehub.com',
        'password' => Hash::make('Password123!'),
    ]);

    $response = $this->actingAs($user)->patch('/profile', [
        'name' => 'Riziq',
        'email' => 'riziq@sparehub.com',
        'current_password' => 'Password123!',
        'password' => 'NewPassword123!',
        'password_confirmation' => 'NewPassword123!',
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertSessionHas('success', 'Profil berhasil diperbarui');
    expect(Hash::check('NewPassword123!', $user->refresh()->password))->toBeTrue();
});

test('TC-WB-R02 Step 5 (B2, B3, B4): Isi current_password salah, isi password baru, klik Simpan', function () {
    $user = User::factory()->create([
        'name' => 'Riziq',
        'email' => 'riziq@sparehub.com',
        'password' => Hash::make('Password123!'),
    ]);

    $response = $this->actingAs($user)->patch('/profile', [
        'name' => 'Riziq',
        'email' => 'riziq@sparehub.com',
        'current_password' => 'WrongPassword123!',
        'password' => 'NewPassword123!',
        'password_confirmation' => 'NewPassword123!',
    ]);

    $response->assertSessionHasErrors('current_password');
    expect(Hash::check('Password123!', $user->refresh()->password))->toBeTrue();
});

test('TC-WB-R02 Step 6 (B2, B9, B11): Matikan Node.js API, lakukan update profil valid', function () {
    $user = User::factory()->create([
        'name' => 'Riziq',
        'email' => 'riziq@sparehub.com',
    ]);

    // Force Node.js API patch to fail
    Http::fake([
        config('services.node_api.url') . '/api/profile/*' => Http::response(['success' => false], 500),
    ]);

    $response = $this->actingAs($user)->patch('/profile', [
        'name' => 'Riziq Updated',
        'email' => 'riziq@sparehub.com',
    ]);

    $response->assertSessionHasErrors('general');
});

test('TC-WB-R02 Step 7 (B13, B14, B18): Upload foto profil baru .jpg (ada foto lama di storage)', function () {
    Storage::fake('public');
    Http::fake([
        config('services.node_api.url') . '/api/profile/*' => Http::response(['success' => true], 200),
    ]);

    $user = User::factory()->create([
        'pfpPath' => asset('storage/avatars/old-avatar.webp'),
    ]);

    // Create the old avatar in fake storage to verify it gets deleted
    Storage::disk('public')->put('avatars/old-avatar.webp', 'old image data');

    $newFile = UploadedFile::fake()->image('new-avatar.jpg');

    $response = $this->actingAs($user)->patch('/edit_profil', [
        'pfpPath' => $newFile,
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertSessionHas('success', 'Foto profil berhasil diperbarui');

    // Assert old file deleted
    Storage::disk('public')->assertMissing('avatars/old-avatar.webp');

    // Assert Node.js API received the sync patch request
    Http::assertSent(function (\Illuminate\Http\Client\Request $request) use ($user) {
        return $request->url() === config('services.node_api.url') . "/api/profile/{$user->id}"
            && $request->method() === 'PATCH'
            && isset($request['pfpPath'])
            && str_contains($request['pfpPath'], 'storage/avatars/');
    });
});

test('TC-WB-R02 Step 8 (B12): Upload file .pdf sebagai foto profil', function () {
    $user = User::factory()->create();

    $invalidFile = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    $response = $this->actingAs($user)->patch('/edit_profil', [
        'pfpPath' => $invalidFile,
    ]);

    $response->assertSessionHasErrors('pfpPath');
    $errors = session('errors')->get('pfpPath');
    expect($errors[0])->toContain('File harus berupa gambar.');
});

test('TC-WB-R02 Step 9 (B12): Submit form tanpa memilih file apapun', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->patch('/edit_profil', [
        'pfpPath' => null,
    ]);

    $response->assertSessionHasErrors('pfpPath');
    $errors = session('errors')->get('pfpPath');
    expect($errors[0])->toContain('Foto profil wajib diunggah.');
});
