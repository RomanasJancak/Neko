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

         $user = User::create([
            'name' => 'Thomas',
            'email' => 'thomas@neko.co.uk',
            'password' => Hash::make('password')
         ]);
         $role = Role::find(2);
         $user->assignRole($role);
                  
         $user = User::create([
            'name' => 'Aurelija',
            'email' => 'aurelija@neko.co.uk',
            'password' => Hash::make('password')
         ]);
         $role = Role::find(3);
         $user->assignRole($role);
                           
         $user = User::create([
            'name' => 'Romanas',
            'email' => 'romanas@neko.co.uk',
            'password' => Hash::make('password')
         ]);
         $role = Role::find(5);
         $user->assignRole($role);

         $user = User::create([
            'name' => 'courier_1',
            'email' => 'courier_1@neko.co.uk',
            'password' => Hash::make('password')
         ]);
         $role = Role::find(5);
         $user->assignRole($role);

         $user = User::create([
            'name' => 'courier_2',
            'email' => 'courier_2@neko.co.uk',
            'password' => Hash::make('password')
         ]);
         $role = Role::find(5);
         $user->assignRole($role);
    }
}
