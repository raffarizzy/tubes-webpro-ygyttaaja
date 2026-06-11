<?php

/**
 * TC-WB-RA01 — RegisteredUserController::store() (Branch Coverage)
 * Feature: FR1 — Registrasi Akun
 * PIC: Raffa Rizky Febryan
 */

use App\Models\User;

// B2 — semua field valid → user dibuat, login otomatis, redirect /
test('TC-WB-RA01 Step 1 — semua field valid: user terdaftar dan redirect ke /', function () {
    $response = $this->post('/register', [
        'name'                  => 'Raffa Test',
        'email'                 => 'raffa.test.' . time() . '@sparehub.com',
        'phone'                 => '08123456789',
        'password'              => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect('/');
});

// B1a — name kosong → error
test('TC-WB-RA01 Step 2 — name kosong: error "The name field is required"', function () {
    $response = $this->post('/register', [
        'name'                  => '',
        'email'                 => 'raffa2@sparehub.com',
        'phone'                 => '08123456789',
        'password'              => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $response->assertSessionHasErrors('name');
    $this->assertGuest();
});

// B1b — email bukan format valid → error
test('TC-WB-RA01 Step 3 — email format tidak valid: error email must be a valid email address', function () {
    $response = $this->post('/register', [
        'name'                  => 'Raffa Test',
        'email'                 => 'bukan-email',
        'phone'                 => '08123456789',
        'password'              => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

// B1c — email duplikat → error
test('TC-WB-RA01 Step 4 — email sudah terdaftar: error "The email has already been taken"', function () {
    $existing = User::factory()->create(['phone' => '08100000003']);

    $response = $this->post('/register', [
        'name'                  => 'Raffa Test',
        'email'                 => $existing->email,
        'phone'                 => '08123456789',
        'password'              => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

// B1d — phone kosong → error
test('TC-WB-RA01 Step 5 — phone kosong: error "The phone field is required"', function () {
    $response = $this->post('/register', [
        'name'                  => 'Raffa Test',
        'email'                 => 'raffa3@sparehub.com',
        'phone'                 => '',
        'password'              => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $response->assertSessionHasErrors('phone');
    $this->assertGuest();
});

// B1f — password != confirmation → error
test('TC-WB-RA01 Step 6 — password confirmation tidak cocok: error confirmation does not match', function () {
    $response = $this->post('/register', [
        'name'                  => 'Raffa Test',
        'email'                 => 'raffa4@sparehub.com',
        'phone'                 => '08123456789',
        'password'              => 'Password123!',
        'password_confirmation' => 'Password456!',
    ]);

    $response->assertSessionHasErrors('password');
    $this->assertGuest();
});

// B1e — password terlalu pendek < 8 karakter → error
test('TC-WB-RA01 Step 7 — password kurang dari 8 karakter: error validasi password', function () {
    $response = $this->post('/register', [
        'name'                  => 'Raffa Test',
        'email'                 => 'raffa5@sparehub.com',
        'phone'                 => '08123456789',
        'password'              => 'short',
        'password_confirmation' => 'short',
    ]);

    $response->assertSessionHasErrors('password');
    $this->assertGuest();
});

// B2 — verifikasi pfpPath default ter-set setelah registrasi sukses
test('TC-WB-RA01 Step 8 — setelah registrasi sukses pfpPath default ter-set', function () {
    $email = 'raffa.pfp.' . time() . '@sparehub.com';

    $this->post('/register', [
        'name'                  => 'Raffa PFP Test',
        'email'                 => $email,
        'phone'                 => '08123456789',
        'password'              => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $user = User::where('email', $email)->first();
    expect($user->pfpPath)->not->toBeNull();
    expect($user->pfpPath)->toContain('default-avatar');
});
