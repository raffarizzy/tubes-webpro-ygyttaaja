<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('[B2] new users can register with valid data', function () {
    Event::fake();

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '081234567890',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect('/');

    $this->assertDatabaseHas('users', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '081234567890',
    ]);

    Event::assertDispatched(Registered::class);
});

test('[B1a] registration fails when name is empty', function () {
    $response = $this->post('/register', [
        'name' => '',
        'email' => 'test@example.com',
        'phone' => '081234567890',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    fwrite(STDOUT, "\n=== TEST B1a ===\n");
    dump(['errors' => session('errors')?->all()]);

    $response->assertSessionHasErrors(['name']);
});

test('[B1a] registration fails when name exceeds 255 characters', function () {
    $response = $this->post('/register', [
        'name' => str_repeat('a', 256),
        'email' => 'test@example.com',
        'phone' => '081234567890',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors(['name']);
});

test('[B1b] registration fails when email is not in email format', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'not-an-email',
        'phone' => '081234567890',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors(['email']);
});

test('[B1c] registration fails when email is already taken', function () {
    User::factory()->create([
        'email' => 'taken@example.com',
        'phone' => '081111111111',
    ]);

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'taken@example.com',
        'phone' => '081234567890',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors(['email']);
});

test('[B1d] registration fails when phone is empty', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors(['phone']);
});

test('[B1d] registration fails when phone exceeds 20 characters', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => str_repeat('0', 21),
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors(['phone']);
});

test('[B1e] registration fails when password does not meet defaults', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '081234567890',
        'password' => 'short',
        'password_confirmation' => 'short',
    ]);

    $response->assertSessionHasErrors(['password']);
});

test('[B1f] registration fails when password does not match confirmation', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '081234567890',
        'password' => 'password123',
        'password_confirmation' => 'different123',
    ]);

    $response->assertSessionHasErrors(['password']);
});
