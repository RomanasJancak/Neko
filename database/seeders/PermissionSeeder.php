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
            'job-create-chooseAnyPostalCode',
            'invoice-view',
            'invoice-create',
            'invoice-edit',
            'invoice-delete',
            'jobtemplate-view',
            'jobtemplate-create',
            'jobtemplate-edit',
            'jobtemplate-delete',
            'bike-view',
            'bike-create',
            'bike-edit',
            'bike-delete',
            'extratype-view',
            'extratype-create',
            'extratype-edit',
            'extratype-delete',
            'addonrule-view',
            'addonrule-create',
            'addonrule-edit',
            'addonrule-delete',
            'status-view',
            'status-create',
            'status-edit',
            'status-delete',
            'packageType-view',
            'packageType-create',
            'packageType-edit',
            'packageType-delete',
            'approvedpostalcodearea-view',
            'approvedpostalcodearea-create',
            'approvedpostalcodearea-edit',
            'approvedpostalcodearea-delete',
        ];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => $guardName,
            ]);
        }
    }
}
