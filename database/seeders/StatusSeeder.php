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
        foreach($statuses as $status){
            Status::create(['name'=> $status]);
        }
    }
}
