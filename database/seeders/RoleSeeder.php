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
        $superAdmin     = Role::create([  'name'      =>  'superadmin']);
        $superAdmin->syncPermissions(Permission::all());
        $admin      = Role::create([  'name'      =>  'admin']);
        $moderator  = Role::create([  'name'      =>  'moderator']);
        $client     = Role::create([  'name'      =>  'client']);
        $courier    = Role::create([  'name'      =>  'courier']);
    }
}
