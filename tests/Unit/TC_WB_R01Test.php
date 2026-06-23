<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\PasswordReset;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| TC-WB-R01 — NewPasswordController::store (Branch & Validation Coverage)
|--------------------------------------------------------------------------
*/

test('TC-WB-R01 Step 1 (Success): Reset password dengan token, email, dan password valid', function () {
    Event::fake();

    $user = User::factory()->create([
        'email' => 'riziq@sparehub.com',
        'password' => Hash::make('PasswordLama123!'),
    ]);

    // Generate valid reset token
    $token = Password::createToken($user);

    $response = $this->post('/reset-password', [
        'token' => $token,
        'email' => 'riziq@sparehub.com',
        'password' => 'PasswordBaru123!',
        'password_confirmation' => 'PasswordBaru123!',
    ]);

    // Assert redirect to login route
    $response->assertRedirect(route('login'));
    $response->assertSessionHas('status');

    // Assert password updated in database
    $user->refresh();
    expect(Hash::check('PasswordBaru123!', $user->password))->toBeTrue();

    // Assert PasswordReset event was dispatched
    Event::assertDispatched(PasswordReset::class, function ($event) use ($user) {
        return $event->user->id === $user->id;
    });
});

test('TC-WB-R01 Step 2 (Failure): Gagal reset password karena token tidak valid', function () {
    $user = User::factory()->create([
        'email' => 'riziq@sparehub.com',
        'password' => Hash::make('PasswordLama123!'),
    ]);

    $response = $this->post('/reset-password', [
        'token' => 'invalid-token-12345',
        'email' => 'riziq@sparehub.com',
        'password' => 'PasswordBaru123!',
        'password_confirmation' => 'PasswordBaru123!',
    ]);

    $response->assertSessionHasErrors('email');
    
    // Assert password did not change
    $user->refresh();
    expect(Hash::check('PasswordLama123!', $user->password))->toBeTrue();
});

test('TC-WB-R01 Step 3 (Failure): Gagal reset password karena email tidak terdaftar', function () {
    $user = User::factory()->create([
        'email' => 'riziq@sparehub.com',
        'password' => Hash::make('PasswordLama123!'),
    ]);

    $token = Password::createToken($user);

    $response = $this->post('/reset-password', [
        'token' => $token,
        'email' => 'nonexistent@sparehub.com',
        'password' => 'PasswordBaru123!',
        'password_confirmation' => 'PasswordBaru123!',
    ]);

    $response->assertSessionHasErrors('email');
    
    // Assert password did not change
    $user->refresh();
    expect(Hash::check('PasswordLama123!', $user->password))->toBeTrue();
});

test('TC-WB-R01 Step 4 (Validation): Gagal reset password karena konfirmasi password tidak cocok', function () {
    $user = User::factory()->create([
        'email' => 'riziq@sparehub.com',
        'password' => Hash::make('PasswordLama123!'),
    ]);

    $token = Password::createToken($user);

    $response = $this->post('/reset-password', [
        'token' => $token,
        'email' => 'riziq@sparehub.com',
        'password' => 'PasswordBaru123!',
        'password_confirmation' => 'PasswordBeda123!',
    ]);

    $response->assertSessionHasErrors('password');
    
    // Assert password did not change
    $user->refresh();
    expect(Hash::check('PasswordLama123!', $user->password))->toBeTrue();
});

test('TC-WB-R01 Step 5 (Validation): Gagal reset password karena field email kosong', function () {
    $user = User::factory()->create([
        'email' => 'riziq@sparehub.com',
        'password' => Hash::make('PasswordLama123!'),
    ]);

    $token = Password::createToken($user);

    $response = $this->post('/reset-password', [
        'token' => $token,
        'email' => '',
        'password' => 'PasswordBaru123!',
        'password_confirmation' => 'PasswordBaru123!',
    ]);

    $response->assertSessionHasErrors('email');
});

test('TC-WB-R01 Step 6 (Validation): Gagal reset password karena password terlalu pendek', function () {
    $user = User::factory()->create([
        'email' => 'riziq@sparehub.com',
        'password' => Hash::make('PasswordLama123!'),
    ]);

    $token = Password::createToken($user);

    $response = $this->post('/reset-password', [
        'token' => $token,
        'email' => 'riziq@sparehub.com',
        'password' => '123',
        'password_confirmation' => '123',
    ]);

    $response->assertSessionHasErrors('password');
});

test('TC-WB-R01 Step 7 (Validation): Gagal reset password karena token kosong', function () {
    $user = User::factory()->create([
        'email' => 'riziq@sparehub.com',
        'password' => Hash::make('PasswordLama123!'),
    ]);

    $response = $this->post('/reset-password', [
        'token' => '',
        'email' => 'riziq@sparehub.com',
        'password' => 'PasswordBaru123!',
        'password_confirmation' => 'PasswordBaru123!',
    ]);

    $response->assertSessionHasErrors('token');
});
