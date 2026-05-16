<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_via_api_and_receive_token(): void
    {
        $user = User::factory()->create([
            'password' => 'password123',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Login successful.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'token',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'roles',
                ],
            ]);

        $this->assertNotEmpty($response->json('token'));
    }

    public function test_user_cannot_login_via_api_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'password' => 'password123',
        ]);

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'invalid-password',
        ])->assertStatus(401)
            ->assertJson([
                'success' => false,
                'error' => 'Invalid credentials.',
            ]);
    }
}
