<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\PostalCode;

class PostalCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $postalCodes = [
            'N1',
            'N4', 'N5', 'N7', 'N16', 'N19',
            'W1', 'NW1', 'NW5',
            'WC1', 'WC2',
            'EC1', 'EC2', 'EC3', 'EC4', 
            'E1', 'E2', 'E5', 'E8', 'E9',
        ];
        foreach($postalCodes as $postalCode){
            PostalCode::create(['name'=> $postalCode]);
        }
    }
}
