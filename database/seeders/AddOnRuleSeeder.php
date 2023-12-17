<?php

namespace Database\Seeders;

use App\Models\AddonRule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddOnRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AddonRule::create([
                'begin_date'                =>  '2023-12-01 00:00:00','end_date' => '2023-12-06 23:59:59',
                'baseprice'                 =>  8,
                'distancerule_1_name'       =>  'Up to 3 miles',                'distancerule_1_value'  =>  2,
                'distancerule_2_name'       =>  'Up to 5 miles',                'distancerule_2_value'  =>  6,
                'extradistancerule_name'    =>  'Over 5 miles',                 'extradistancerule_value'    =>  3,
                'rule_1_name'               =>  'Outside post code list/zone',  'rule_1_value'    =>  6,
                'rule_2_name'               =>  'Food (hot or cold)',           'rule_2_value'    =>  2,
                'rule_3_name'               =>  'Oversize',                     'rule_3_value'    =>  2,
                'rule_4_name'               =>  'Fragile Items',                'rule_4_value'    =>  2,
                'rule_5_name'               =>  'Timed',                        'rule_5_value'    =>  3,
                'rule_6_name'               =>  'Same-Day',                     'rule_6_value'    =>  4,
                'rule_7_name'               =>  'Rush job',                     'rule_7_value'    =>  6,
                'rule_8_name'               =>  'Waiting time over 10 mins',    'rule_8_value'    =>  2,
                'rule_9_name'               =>  'Return trip to p/u same day ', 'rule_9_value'    =>  4,
                'rule_10_name'              =>  'Cancelations up to 2h',        'rule_10_value'    =>  8,
                'rule_11_name'              =>  'Cancelations when courier is in P/U','rule_11_value'    =>  'Full delivery price',
                'rule_12_name'              =>  'Cancelations when courier is in DROP','rule_12_value'    =>  'Full delivery price + return to P/U',
                'rule_13_name'              =>  'Sunday/Bank holiday','rule_13_value'    =>  2,
                'rule_14_name'              =>  'out of hours ','rule_14_value'    =>  4,
            ]);
            AddonRule::create([
                'begin_date'                =>  '2023-12-07 00:00:00','end_date' => '2023-12-14 23:59:59',
                'baseprice'                 =>  8,
                'distancerule_1_name'       =>  'Up to 3 miles',                'distancerule_1_value'  =>  2,
                'distancerule_2_name'       =>  'Up to 5 miles',                'distancerule_2_value'  =>  6,
                'extradistancerule_name'    =>  'Over 5 miles',                 'extradistancerule_value'    =>  3,
                'rule_1_name'               =>  'Outside post code list/zone',  'rule_1_value'    =>  6,
                'rule_2_name'               =>  'Food (hot or cold)',           'rule_2_value'    =>  2,
                'rule_3_name'               =>  'Oversize',                     'rule_3_value'    =>  2,
                'rule_4_name'               =>  'Fragile Items',                'rule_4_value'    =>  2,
                'rule_5_name'               =>  'Timed',                        'rule_5_value'    =>  3,
                'rule_6_name'               =>  'Same-Day',                     'rule_6_value'    =>  4,
                'rule_7_name'               =>  'Rush job',                     'rule_7_value'    =>  6,
                'rule_8_name'               =>  'Waiting time over 10 mins',    'rule_8_value'    =>  2,
                'rule_9_name'               =>  'Return trip to p/u same day ', 'rule_9_value'    =>  4,
                'rule_10_name'              =>  'Cancelations up to 2h',        'rule_10_value'    =>  8,
                'rule_11_name'              =>  'Cancelations when courier is in P/U','rule_11_value'    =>  'Full delivery price',
                'rule_12_name'              =>  'Cancelations when courier is in DROP','rule_12_value'    =>  'Full delivery price + return to P/U',
                'rule_13_name'              =>  'Sunday/Bank holiday','rule_13_value'    =>  2,
                'rule_14_name'              =>  'out of hours ','rule_14_value'    =>  4,
            ]);
            AddonRule::create([
                'begin_date'                =>  '2023-12-14 00:00:00','end_date' => '2023-12-21 23:59:59',
                'baseprice'                 =>  8,
                'distancerule_1_name'       =>  'Up to 3 miles',                'distancerule_1_value'  =>  2,
                'distancerule_2_name'       =>  'Up to 5 miles',                'distancerule_2_value'  =>  6,
                'extradistancerule_name'    =>  'Over 5 miles',                 'extradistancerule_value'    =>  3,
                'rule_1_name'               =>  'Outside post code list/zone',  'rule_1_value'    =>  6,
                'rule_2_name'               =>  'Food (hot or cold)',           'rule_2_value'    =>  2,
                'rule_3_name'               =>  'Oversize',                     'rule_3_value'    =>  2,
                'rule_4_name'               =>  'Fragile Items',                'rule_4_value'    =>  2,
                'rule_5_name'               =>  'Timed',                        'rule_5_value'    =>  3,
                'rule_6_name'               =>  'Same-Day',                     'rule_6_value'    =>  4,
                'rule_7_name'               =>  'Rush job',                     'rule_7_value'    =>  6,
                'rule_8_name'               =>  'Waiting time over 10 mins',    'rule_8_value'    =>  2,
                'rule_9_name'               =>  'Return trip to p/u same day ', 'rule_9_value'    =>  4,
                'rule_10_name'              =>  'Cancelations up to 2h',        'rule_10_value'    =>  8,
                'rule_11_name'              =>  'Cancelations when courier is in P/U','rule_11_value'    =>  'Full delivery price',
                'rule_12_name'              =>  'Cancelations when courier is in DROP','rule_12_value'    =>  'Full delivery price + return to P/U',
                'rule_13_name'              =>  'Sunday/Bank holiday','rule_13_value'    =>  2,
                'rule_14_name'              =>  'out of hours ','rule_14_value'    =>  4,
            ]);
    }
}
// $tabel->string('rule_1_name');
// $tabel->string('rule_1_value');
// $tabel->string('rule_2_name');
// $tabel->string('rule_2_value');
// $tabel->string('rule_3_name');
// $tabel->string('rule_3_value');
// $tabel->string('rule_4_name');
// $tabel->string('rule_4_value');
// $tabel->string('rule_5_name');
// $tabel->string('rule_5_value');
// $tabel->string('rule_6_name');
// $tabel->string('rule_6_value');
// $tabel->string('rule_7_name');
// $tabel->string('rule_7_value');
// $tabel->string('rule_8_name');
// $tabel->string('rule_8_value');
// $tabel->string('rule_9_name');
// $tabel->string('rule_9_value');
// $tabel->string('rule_10_name');
// $tabel->string('rule_10_value');
// $tabel->string('rule_11_name');
// $tabel->string('rule_11_value');
// $tabel->string('rule_12_name');
// $tabel->string('rule_12_value');
// $tabel->string('rule_13_name');
// $tabel->string('rule_13_value');
// $tabel->string('rule_14_name');
// $tabel->string('rule_14_value');