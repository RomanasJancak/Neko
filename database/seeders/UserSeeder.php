<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\Hash;

use App\Models\User;
use Spatie\Permission\Models\Permission;
use App\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         //
         $user = User::create([
            'name' => 'SAdmin',
            'email' => 'SAdmin@localhost.lt',
            'password' => Hash::make('password')
         ]);
         $role = Role::find(1);
         $user->assignRole($role);
    }
}
