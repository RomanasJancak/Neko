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
            $table->string('name');//temp
            $table->string('display_name');
            $table->unsignedBigInteger('price');//temp
            $table->unsignedBigInteger('client_id');//temp
            //--------OLD----------
            // $table->float('baseprice');
            // $table->string('distancerule_1_name')->nullable();// up to 3 miles
            // $table->float('distancerule_1_value')->nullable();
            // $table->string('distancerule_2_name')->nullable();// up to 5 miles
            // $table->float('distancerule_2_value')->nullable();
            // $table->string('extradistancerule_name')->nullable(); // extra mile cost over 2 rule
            // $table->string('extradistancerule_value')->nullable();
            // $table->string('rule_1_name')->nullable();
            // $table->string('rule_1_value')->nullable();
            // $table->string('rule_2_name')->nullable();
            // $table->string('rule_2_value')->nullable();
            // $table->string('rule_3_name')->nullable();
            // $table->string('rule_3_value')->nullable();
            // $table->string('rule_4_name')->nullable();
            // $table->string('rule_4_value')->nullable();
            // $table->string('rule_5_name')->nullable();
            // $table->string('rule_5_value')->nullable();
            // $table->string('rule_6_name')->nullable();
            // $table->string('rule_6_value')->nullable();
            // $table->string('rule_7_name')->nullable();
            // $table->string('rule_7_value')->nullable();
            // $table->string('rule_8_name')->nullable();
            // $table->string('rule_8_value')->nullable();
            // $table->string('rule_9_name')->nullable();
            // $table->string('rule_9_value')->nullable();
            // $table->string('rule_10_name')->nullable();
            // $table->string('rule_10_value')->nullable();
            // $table->string('rule_11_name')->nullable();
            // $table->string('rule_11_value')->nullable();
            // $table->string('rule_12_name')->nullable();
            // $table->string('rule_12_value')->nullable();
            // $table->string('rule_13_name')->nullable();
            // $table->string('rule_13_value')->nullable();
            // $table->string('rule_14_name')->nullable();
            // $table->string('rule_14_value')->nullable();
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
