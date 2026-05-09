<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class UserCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function test_admin_can_perform_user_crud_via_web_routes(): void
    {
        $superAdminRole = Role::where('name', 'superadmin')->firstOrFail();
        $adminRole = Role::where('name', 'admin')->firstOrFail();

        $actingUser = User::where('email', 'SAdmin@localhost.lt')->firstOrFail();

        $this->actingAs($actingUser);

        $this->get(route('user.index'))->assertOk();
        $this->get(route('user.create'))->assertOk();

        $this->post(route('user.store'), [
            'name' => 'CRUD Target',
            'email' => 'crud-target@example.com',
            'phone' => '+37061234567',
            'role' => $adminRole->id,
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ])->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'crud-target@example.com',
            'name' => 'CRUD Target',
            'phone' => '+37061234567',
        ]);

        $createdUser = User::where('email', 'crud-target@example.com')->firstOrFail();
        $this->assertTrue($createdUser->roles()->where('id', $adminRole->id)->exists());

        $this->get(route('user.show', $createdUser))->assertOk();

        $this->patch(route('user.update', $createdUser), [
            'user_name' => 'CRUD Updated',
            'user_email' => 'crud-updated@example.com',
            'phone' => '+37069999999',
            'role' => $superAdminRole->id,
        ])->assertRedirect(route('user.show', $createdUser));

        $this->assertDatabaseHas('users', [
            'id' => $createdUser->id,
            'name' => 'CRUD Updated',
            'email' => 'crud-updated@example.com',
            'phone' => '+37069999999',
        ]);

        $createdUser->refresh();
        $this->assertTrue($createdUser->roles()->where('id', $superAdminRole->id)->exists());

        $this->delete(route('user.destroy', $createdUser))
            ->assertRedirect(route('user.index'));

        $this->assertDatabaseMissing('users', [
            'id' => $createdUser->id,
        ]);
    }

    public function test_guest_is_redirected_from_user_routes(): void
    {
        $this->get(route('user.index'))->assertRedirect(route('login'));
        $this->get(route('user.create'))->assertRedirect(route('login'));
    }
}
