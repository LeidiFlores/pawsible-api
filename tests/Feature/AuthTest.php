<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_receive_token(): void
    {
        $response = $this->postJson('/api/v1/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertCreated()
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'user' => ['id', 'name', 'email'],
            ])
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('user.email', 'jane@example.com');

        $this->assertDatabaseHas('users', [
            'email' => 'jane@example.com',
        ]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'name' => 'auth_token',
            'tokenable_type' => User::class,
            'tokenable_id' => $response->json('user.id'),
        ]);
    }

    public function test_register_returns_json_validation_errors(): void
    {
        $response = $this->post('/api/v1/register', [
            'email' => 'not-an-email',
            'password' => 'short',
            'password_confirmation' => 'different',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_user_can_login_and_receive_token(): void
    {
        $user = User::factory()->create([
            'email' => 'jane@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'jane@example.com',
            'password' => 'password123',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'user' => ['id', 'name', 'email'],
            ])
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('user.id', $user->id);

        $this->assertDatabaseHas('personal_access_tokens', [
            'name' => 'auth_token',
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
        ]);
    }

    public function test_login_rejects_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'jane@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'jane@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertUnauthorized()
            ->assertJsonPath('message', 'Invalid credentials');

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_authenticated_user_endpoint_returns_current_user(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/v1/user');

        $response->assertOk()
            ->assertJsonPath('id', $user->id)
            ->assertJsonPath('email', $user->email);
    }

    public function test_user_can_logout_and_revoke_current_token(): void
    {
        $user = User::factory()->create();
        $plainTextToken = $user->createToken('auth_token')->plainTextToken;
        $tokenId = explode('|', $plainTextToken, 2)[0];

        $response = $this->withToken($plainTextToken)->postJson('/api/v1/logout');

        $response->assertOk()
            ->assertJsonPath('message', 'Logged out successfully');

        $this->assertNull(PersonalAccessToken::find($tokenId));
    }

    public function test_logout_requires_authentication(): void
    {
        $response = $this->postJson('/api/v1/logout');

        $response->assertUnauthorized();
    }
}
