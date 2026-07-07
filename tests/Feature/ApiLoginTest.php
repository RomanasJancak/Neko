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

    public function test_user_can_login_via_v1_auth_endpoint_and_receive_contract_payload(): void
    {
        $user = User::factory()->create([
            'password' => 'password123',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
            'device_name' => 'react-web',
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
                    'permissions',
                ],
                'data' => [
                    'token',
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'roles',
                        'permissions',
                    ],
                ],
            ]);
    }

    public function test_authenticated_user_can_fetch_current_profile_from_v1_me_endpoint(): void
    {
        $user = User::factory()->create([
            'password' => 'password123',
        ]);
        $token = $user->createToken('react-web')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'roles',
                        'permissions',
                    ],
                ],
            ]);
    }

    public function test_authenticated_user_can_logout_current_token_only(): void
    {
        $user = User::factory()->create([
            'password' => 'password123',
        ]);
        $token = $user->createToken('react-web')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/auth/logout')
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Logged out from current session.',
            ]);

        $this->assertCount(0, $user->fresh()->tokens);
    }

    public function test_authenticated_user_can_logout_all_tokens(): void
    {
        $user = User::factory()->create([
            'password' => 'password123',
        ]);
        $token = $user->createToken('react-web')->plainTextToken;
        $user->createToken('mobile-app');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/auth/logout-all')
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Logged out from all sessions.',
            ]);

        $this->assertCount(0, $user->fresh()->tokens);
    }
}
