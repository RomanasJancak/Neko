<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guardName = 'web';

        $permissions = [
            'role-view',
            'role-create',
            'role-edit',
            'role-delete',
            'permission-view',
            'permission-create',
            'permission-edit',
            'permission-delete',
            'setting-view',
            'setting-create',
            'setting-edit',
            'setting-delete',
            'user-view',
            'user-create',
            'user-edit',
            'user-delete',
            'client-view',
            'client-create',
            'client-edit',
            'client-delete',
            'job-view',
            'job-create',
            'job-edit',
            'job-delete',
            'job-create-chooseAnyPostalCode'
        ];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => $guardName,
            ]);
        }
    }
}
