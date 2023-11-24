<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Status;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            'unassigned',//1
            'assigned',//2
            'accepted',//3
            'completed',//4
            'declined',//5
            'issue',//6
            'proposed',//7
            'completedwithIssue',//8
        ];
        $colors =   [
            '#808080',
            '#3d85c6',
            '#d9ead3',
            '#274e13',
            '#a64d79',
            '#cc0000',
            '#7f6000',
            '#e69138',
        ];
        foreach ($statuses as $index => $status) {
            Status::create([
                'name' => $status,
                'color' => $colors[$index], // Use the corresponding color from the $colors array
            ]);
        }
    }
}
