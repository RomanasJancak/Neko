<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('add_on_rules', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->dateTime('begin_date');
            $table->dateTime('end_date');
            $table->float('baseprice');
            $table->string('distancerule_1_name');// up to 3 miles
            $table->float('distancerule_1_value');
            $table->string('distancerule_2_name');// up to 5 miles
            $table->float('distancerule_2_value');
            $table->string('extradistancerule_name'); // extra mile cost over 2 rule
            $table->string('extradistancerule_value');
            $table->string('rule_1_name');
            $table->string('rule_1_value');
            $table->string('rule_2_name');
            $table->string('rule_2_value');
            $table->string('rule_3_name');
            $table->string('rule_3_value');
            $table->string('rule_4_name');
            $table->string('rule_4_value');
            $table->string('rule_5_name');
            $table->string('rule_5_value');
            $table->string('rule_6_name');
            $table->string('rule_6_value');
            $table->string('rule_7_name');
            $table->string('rule_7_value');
            $table->string('rule_8_name');
            $table->string('rule_8_value');
            $table->string('rule_9_name');
            $table->string('rule_9_value');
            $table->string('rule_10_name');
            $table->string('rule_10_value');
            $table->string('rule_11_name');
            $table->string('rule_11_value');
            $table->string('rule_12_name');
            $table->string('rule_12_value');
            $table->string('rule_13_name');
            $table->string('rule_13_value');
            $table->string('rule_14_name');
            $table->string('rule_14_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('add_on_rules');
    }
};
