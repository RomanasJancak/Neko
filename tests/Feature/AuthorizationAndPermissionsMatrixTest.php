<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AuthorizationAndPermissionsMatrixTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // This project treats role IDs 1 and 2 as superadmin/admin.
        Role::create([
            'name' => 'superadmin',
            'display_name' => 'superadmin',
            'guard_name' => 'web',
        ]);

        Role::create([
            'name' => 'admin',
            'display_name' => 'admin',
            'guard_name' => 'web',
        ]);
    }

    public function test_permissions_matrix_requires_permission_view(): void
    {
        $user = User::factory()->create(['password' => 'password']);

        $this->actingAs($user)
            ->get(route('role.permissionsMatrix'))
            ->assertForbidden();
    }

    public function test_permissions_matrix_can_be_viewed_with_permission_view(): void
    {
        $permissionView = Permission::create(['name' => 'permission-view', 'guard_name' => 'web']);

        $role = Role::create([
            'name' => 'matrix-viewer',
            'display_name' => 'Matrix Viewer',
            'guard_name' => 'web',
        ]);
        $role->givePermissionTo($permissionView);

        $user = User::factory()->create(['password' => 'password']);
        $user->assignRole($role);

        $this->actingAs($user)
            ->get(route('role.permissionsMatrix'))
            ->assertOk();
    }

    public function test_update_permissions_rejects_escalation_attempts(): void
    {
        $permissionView = Permission::create(['name' => 'permission-view', 'guard_name' => 'web']);
        $permissionEdit = Permission::create(['name' => 'permission-edit', 'guard_name' => 'web']);
        $userView = Permission::create(['name' => 'user-view', 'guard_name' => 'web']);

        $editorRole = Role::create([
            'name' => 'editor-role',
            'display_name' => 'Editor Role',
            'guard_name' => 'web',
        ]);
        $editorRole->givePermissionTo([$permissionView, $permissionEdit]);

        $targetRole = Role::create([
            'name' => 'target-role',
            'display_name' => 'Target Role',
            'guard_name' => 'web',
        ]);

        $user = User::factory()->create(['password' => 'password']);
        $user->assignRole($editorRole);

        $this->actingAs($user)
            ->patchJson(route('role.updatePermissions', $targetRole), [
                'permissions' => [$userView->id],
            ])
            ->assertForbidden()
            ->assertJson([
                'message' => 'You cannot assign permissions you do not have.',
            ]);
    }

    public function test_update_permissions_allows_assigning_owned_permissions(): void
    {
        $permissionView = Permission::create(['name' => 'permission-view', 'guard_name' => 'web']);
        $permissionEdit = Permission::create(['name' => 'permission-edit', 'guard_name' => 'web']);

        $editorRole = Role::create([
            'name' => 'editor-role-2',
            'display_name' => 'Editor Role 2',
            'guard_name' => 'web',
        ]);
        $editorRole->givePermissionTo([$permissionView, $permissionEdit]);

        $targetRole = Role::create([
            'name' => 'target-role-2',
            'display_name' => 'Target Role 2',
            'guard_name' => 'web',
        ]);

        $user = User::factory()->create(['password' => 'password']);
        $user->assignRole($editorRole);

        $this->actingAs($user)
            ->patchJson(route('role.updatePermissions', $targetRole), [
                'permissions' => [$permissionView->id],
            ])
            ->assertOk()
            ->assertJson([
                'message' => 'Permissions updated.',
            ]);

        $this->assertTrue($targetRole->fresh()->permissions->contains('id', $permissionView->id));
    }

    public function test_settings_routes_are_permission_protected(): void
    {
        $user = User::factory()->create(['password' => 'password']);

        $this->actingAs($user)
            ->get(route('setting.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->post(route('setting.update'), [])
            ->assertForbidden();
    }
}
