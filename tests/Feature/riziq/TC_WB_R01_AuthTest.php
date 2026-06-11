<?php

/**
 * TC-WB-R01 — AuthenticatedSessionController + LoginRequest (Branch Coverage)
 * Feature: FR2 — Login Sistem
 * PIC: Riziq Rizwan
 */

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    // Reset rate limiter so previous test failures don't bleed over
    $user = User::factory()->create(['phone' => '08100000001']);
    RateLimiter::clear(
        \Illuminate\Support\Str::lower($user->email) . '|127.0.0.1'
    );
    $this->testUser = $user;
});

// B2, B4, B5 — credentials benar → redirect /, user terautentikasi
test('TC-WB-R01 Step 1 — login credentials benar: redirect ke / dan terautentikasi', function () {
    $response = $this->post('/login', [
        'email'    => $this->testUser->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect('/');
});

// B2, B3 — password salah → auth.failed
test('TC-WB-R01 Step 2 — password salah: pesan error credentials dan tetap di login', function () {
    $response = $this->post('/login', [
        'email'    => $this->testUser->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors('email');
});

// B2, B3 — 4 gagal berturut-turut: tiap attempt gagal, counter naik
test('TC-WB-R01 Step 3 — 4 kali login gagal berturut-turut: semua gagal, tidak throttle', function () {
    foreach (range(1, 4) as $attempt) {
        $response = $this->post('/login', [
            'email'    => $this->testUser->email,
            'password' => 'wrong-password',
        ]);
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
});

// B1 — 5 gagal → attempt ke-6 kena throttle
test('TC-WB-R01 Step 4 — login gagal 5 kali lalu attempt ke-6: pesan throttle', function () {
    foreach (range(1, 5) as $attempt) {
        $this->post('/login', [
            'email'    => $this->testUser->email,
            'password' => 'wrong-password',
        ]);
    }

    $response = $this->post('/login', [
        'email'    => $this->testUser->email,
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors('email');
    $errors = session('errors')->get('email');
    expect(implode(' ', $errors))->toMatch('/seconds|minutes|Too many/i');
});

// B6 — logout: session dihapus, redirect /
test('TC-WB-R01 Step 5 — login sukses lalu logout: session dihapus, redirect ke /', function () {
    $response = $this->actingAs($this->testUser)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});

// B4, B5 — login sukses, akses halaman auth /keranjang: tidak di-redirect
test('TC-WB-R01 Step 6 — login sukses, akses /keranjang: halaman terbuka tanpa redirect ke login', function () {
    \Illuminate\Support\Facades\Http::fake([
        'http://localhost:3001/api/cart/*' => \Illuminate\Support\Facades\Http::response(
            ['data' => []], 200
        ),
    ]);

    $response = $this->actingAs($this->testUser)->get('/keranjang');

    $response->assertStatus(200);
});
