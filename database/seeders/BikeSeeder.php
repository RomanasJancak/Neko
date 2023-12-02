<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Bike;

class BikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bikes = [
            'Au-mnium (aura omnium)',//1
            'aE-Omnium (Thomas)',//2
            'BeGreen (bullitt #1)',//3
            'Purple (omnium)',//4
            'Haru (bullitt X #2)',//5
            'Koneko (bullitt #3)',//6
            'Shiny (josh omnium)',//7
        ];
        foreach ($bikes as $bike) {
            Bike::create([
                'name' => $bike,
            ]);
        }
    }
}
