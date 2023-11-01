<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

use App\Models\Day;

class DaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentDate = Carbon::now()->startOfDay();
        
        for ($i = 1; $i <= 5; $i++) {
            Day::create([
                'name' => $currentDate->toDateString(),
                'date' => $currentDate,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Move to the next day
            $currentDate->addDay(); // Add one day to the current date
        }
    }
}
//Status::create(['name'=> $status]);
