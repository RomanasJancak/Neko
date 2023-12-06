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
            'email' => 'thcuenot@gmail.com',
            'password' => Hash::make('password')
         ]);
         $role = Role::find(2);
         $user->assignRole($role);
                  
         $user = User::create([
            'name' => 'Aurelija',
            'email' => 'aurelija.jancak@gmail.com',
            'password' => Hash::make('password')
         ]);
         $role = Role::find(3);
         $user->assignRole($role);
                           
         $user = User::create([
            'name' => 'Andrew New Cat',
            'email' => 'Activisuals@gmail.com',
            'password' => Hash::make('password')
         ]);
         $role = Role::find(5);
         $user->assignRole($role);

         $user = User::create([
            'name' => 'Gabriel Turner',
            'email' => 'gt.xpress98@gmail.com',
            'password' => Hash::make('password')
         ]);
         $role = Role::find(5);
         $user->assignRole($role);

         $user = User::create([
            'name' => 'Gil Neko',
            'email' => 'gilyehezkel1995@gmail.com',
            'password' => Hash::make('password')
         ]);
         $role = Role::find(5);
         $user->assignRole($role);
         
         $user = User::create([
            'name' => 'Josh Hartmann',
            'email' => 'joshua.hartmann4@gmail.com',
            'password' => Hash::make('password')
         ]);
         $role = Role::find(5);
         $user->assignRole($role);

         $user = User::create([
            'name' => 'Jules Neko Cat',
            'email' => 'Juliabicknell6@gmail.com',
            'password' => Hash::make('password')
         ]);
         $role = Role::find(5);
         $user->assignRole($role);
    }
}
