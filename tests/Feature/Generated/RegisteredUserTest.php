<?php

namespace Tests\Feature\Generated;

use App\Models\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisteredUserTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    /**
     * TC-WB-RA01 — Branch B2
     * Valid registration.
     */
    public function test_registration_success(): void
    {
        $response = $this->post('/register', [
            'name' => 'Raffa Test',
            'email' => 'raffa.test@sparehub.com',
            'phone' => '081234567890',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticated();
        
        $user = User::where('email', 'raffa.test@sparehub.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('https://i.ibb.co.com/ZRkqGfJ3/default-avatar-sparehubtize.png', $user->pfpPath);
    }

    /**
     * TC-WB-RA01 — Branch B1a
     * Name is required.
     */
    public function test_registration_fails_if_name_is_missing(): void
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'phone' => '081234567890',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertSessionHasErrors(['name']);
        $this->assertGuest();
    }

    /**
     * TC-WB-RA01 — Branch B1b
     * Invalid email format.
     */
    public function test_registration_fails_if_email_is_invalid(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'not-an-email',
            'phone' => '081234567890',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * TC-WB-RA01 — Branch B1c
     * Email must be unique.
     */
    public function test_registration_fails_if_email_is_already_taken(): void
    {
        User::factory()->create([
            'email' => 'existing@sparehub.com',
        ]);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'existing@sparehub.com',
            'phone' => '081234567890',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * TC-WB-RA01 — Branch B1d
     * Phone is required.
     */
    public function test_registration_fails_if_phone_is_missing(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertSessionHasErrors(['phone']);
    }

    /**
     * TC-WB-RA01 — Branch B1f
     * Password confirmation must match.
     */
    public function test_registration_fails_if_passwords_do_not_match(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '081234567890',
            'password' => 'Password123!',
            'password_confirmation' => 'DifferentPassword',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    /**
     * TC-WB-RA01 — Branch B1e
     * Password must meet complexity/length requirements.
     */
    public function test_registration_fails_if_password_is_too_short(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '081234567890',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors(['password']);
    }
}
