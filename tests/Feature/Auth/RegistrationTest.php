<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('registration screen can be rendered', function () {
    fwrite(STDOUT, "\n[START] Test: Registration screen can be rendered\n");
    $response = $this->get('/register');

    $response->assertStatus(200);
    fwrite(STDOUT, "[INFO] Status: " . $response->status() . " (OK)\n");
});

test('B2: new users can register with valid data', function () {
    fwrite(STDOUT, "\n[SCENARIO B2] Test: Valid Registration (All constraints passed)\n");
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '08123456789',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect('/');
    
    fwrite(STDOUT, "[INFO] User authenticated: " . (auth()->check() ? 'YES' : 'NO') . "\n");
    fwrite(STDOUT, "[INFO] Redirected to: " . $response->headers->get('Location') . "\n");
});

test('B1a: registration fails if name is empty', function () {
    fwrite(STDOUT, "\n[SCENARIO B1a] Test: Name is empty\n");
    $response = $this->post('/register', [
        'name' => '',
        'email' => 'test@example.com',
        'phone' => '08123456789',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors('name');
    
    fwrite(STDOUT, "[SESSION DUMP - EVIDENCE]\n");
    $response->dumpSession(); 
});

test('B1a: registration fails if name is too long', function () {
    fwrite(STDOUT, "\n[SCENARIO B1a] Test: Name is too long (>255 chars)\n");
    $response = $this->post('/register', [
        'name' => str_repeat('a', 256),
        'email' => 'test@example.com',
        'phone' => '08123456789',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors('name');

    fwrite(STDOUT, "[SESSION DUMP - EVIDENCE]\n");
    $response->dumpSession();
});

test('B1b: registration fails if email is not a valid email format', function () {
    fwrite(STDOUT, "\n[SCENARIO B1b] Test: Invalid email format\n");
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'invalid-email',
        'phone' => '08123456789',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors('email');

    fwrite(STDOUT, "[SESSION DUMP - EVIDENCE]\n");
    $response->dumpSession();
});

test('B1c: registration fails if email is already taken', function () {
    fwrite(STDOUT, "\n[SCENARIO B1c] Test: Duplicate email (Unique constraint)\n");
    User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '08123456789',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors('email');

    fwrite(STDOUT, "[SESSION DUMP - EVIDENCE]\n");
    $response->dumpSession();
});

test('B1d: registration fails if phone is empty', function () {
    fwrite(STDOUT, "\n[SCENARIO B1d] Test: Phone is empty\n");
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors('phone');

    fwrite(STDOUT, "[SESSION DUMP - EVIDENCE]\n");
    $response->dumpSession();
});

test('B1e: registration fails if password does not meet Rules::defaults()', function () {
    fwrite(STDOUT, "\n[SCENARIO B1e] Test: Password too short/weak\n");
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '08123456789',
        'password' => 'short',
        'password_confirmation' => 'short',
    ]);

    $response->assertSessionHasErrors('password');

    fwrite(STDOUT, "[SESSION DUMP - EVIDENCE]\n");
    $response->dumpSession();
});

test('B1f: registration fails if password confirmation does not match', function () {
    fwrite(STDOUT, "\n[SCENARIO B1f] Test: Password confirmation mismatch\n");
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '08123456789',
        'password' => 'password123',
        'password_confirmation' => 'mismatch123',
    ]);

    $response->assertSessionHasErrors('password');

    fwrite(STDOUT, "[SESSION DUMP - EVIDENCE]\n");
    $response->dumpSession();
});
