<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Role;
use App\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $superAdmin = Role::firstOrCreate(
            ['name' => 'superadmin', 'guard_name' => 'web'],
            ['display_name' => 'superadmin']
        );
        $superAdmin->syncPermissions(Permission::all());

        $admin = Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'web'],
            ['display_name' => 'admin']
        );
        $admin->syncPermissions(Permission::all());

        $manager = Role::firstOrCreate(
            ['name' => 'manager', 'guard_name' => 'web'],
            ['display_name' => 'manager']
        );
        $manager->syncPermissions([
            'user-view',
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
            'setting-view',
            'setting-edit'
        ]);

        $client = Role::firstOrCreate(
            ['name' => 'client_admin', 'guard_name' => 'web'],
            ['display_name' => 'admin']
        );
        $client->syncPermissions([
            'user-view',
            'client-view',
            'client-edit',
            'job-view',
            'job-create',
            'job-edit',
            'job-delete',
            'invoice-view',
            'invoice-create',
            'invoice-edit',
            'invoice-delete',
        ]);

        $courier = Role::firstOrCreate(
            ['name' => 'courier', 'guard_name' => 'web'],
            ['display_name' => 'courier']
        );
        $courier->syncPermissions([
            'user-view',
            'client-view',
            'job-view',
            'job-edit',
        ]);

    }
}
