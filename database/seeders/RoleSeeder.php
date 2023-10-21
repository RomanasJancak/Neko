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
        $superAdmin     = Role::create([  'name'      =>  'superadmin']);
        $superAdmin->syncPermissions(Permission::all());
        $admin      = Role::create([  'name'      =>  'admin']);
        $admin->syncPermissions(Permission::all());
        $manager  = Role::create([  'name'      =>  'manager']);
        $manager->givePermissionTo([
            'user-view',
            'client-view',
            'client-create',
            'client-edit',
            'client-delete',
            'job-view',
            'job-create',
            'job-edit',
            'job-delete',
        ]);
        $client     = Role::create([  'name'      =>  'client']);
        $client->givePermissionTo([
            'user-view',
            'client-view',
            'client-edit',
            'job-view',
            'job-create',
            'job-edit',
            'job-delete',
        ]);
        $courier    = Role::create([  'name'      =>  'courier']);
        $courier->givePermissionTo([
            'user-view',
            'client-view',
            'job-view',
            'job-edit',
        ]);
    }
}
