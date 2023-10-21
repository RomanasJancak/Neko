<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            StatusSeeder::class,
            
        ]);
        \App\Models\User::factory(100)->create();
        \App\Models\Client::factory(100)->create();
        \App\Models\Job::factory(100)->create();
    }
}
