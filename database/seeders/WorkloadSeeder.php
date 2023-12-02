<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Workload;

class WorkloadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Workload::create(['capacity'  =>  '100%'    ,'day_id'    =>  1,'user_id'   =>  4,'bike_id'   =>  1,]);
        Workload::create(['capacity'  =>  '75%'     ,'day_id'    =>  1,'user_id'   =>  5,'bike_id'   =>  2,]);
        Workload::create(['capacity'  =>  'drunk'   ,'day_id'    =>  1,'user_id'   =>  6,'bike_id'   =>  3,]);

        Workload::create(['capacity'  =>  'sick'    ,'day_id'    =>  2,'user_id'   =>  4,'bike_id'   =>  1,]);
        Workload::create(['capacity'  =>  'tired'   ,'day_id'    =>  2,'user_id'   =>  5,'bike_id'   =>  3,]);
        Workload::create(['capacity'  =>  '100%'    ,'day_id'    =>  2,'user_id'   =>  6,'bike_id'   =>  2,]);

        Workload::create(['capacity'  =>  '10%','day_id'    =>  3,'user_id'   =>  4,'bike_id'   =>  2,]);
        Workload::create(['capacity'  =>  '100%','day_id'    =>  3,'user_id'   =>  5,'bike_id'   =>  3,]);
        Workload::create(['capacity'  =>  '100%','day_id'    =>  3,'user_id'   =>  6,'bike_id'   =>  1,]);

        Workload::create(['capacity'  =>  '100%','day_id'    =>  4,'user_id'   =>  4,'bike_id'   =>  2,]);
        Workload::create(['capacity'  =>  '10%','day_id'    =>  4,'user_id'   =>  5,'bike_id'   =>  1,]);
        Workload::create(['capacity'  =>  '100%','day_id'    =>  4,'user_id'   =>  6,'bike_id'   =>  3,]);

        Workload::create(['capacity'  =>  '100%','day_id'    =>  5,'user_id'   =>  4,'bike_id'   =>  3,]);
        Workload::create(['capacity'  =>  '100%','day_id'    =>  5,'user_id'   =>  5,'bike_id'   =>  1,]);
        Workload::create(['capacity'  =>  '10%','day_id'    =>  5,'user_id'   =>  6,'bike_id'   =>  2,]);       
    }
}
