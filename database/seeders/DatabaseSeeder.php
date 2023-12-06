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
        
        //\App\Models\Client::factory(100)->create();
        $this->call([
            ClientSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            StatusSeeder::class,
            DaySeeder::class,
            JobSeeder::class,
            BikeSeeder::class,
            WorkloadSeeder::class,
            AddOnRuleSeeder::class,
        ]);
        //\App\Models\User::factory(100)->create();
    }
}
