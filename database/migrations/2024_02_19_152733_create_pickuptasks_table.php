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
        Schema::create('pickuptasks', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('status_id');
            $table->dateTime('pickup_time_begin');
            $table->dateTime('pickup_time_end');
            $table->unsignedBigInteger('address_id')->nullable();
            $table->string('pickupclientname')->nullable();
            $table->string('pickupclientaddressline')->nullable();                        
            $table->string('pickupclientcity')->nullable();
            $table->string('pickupclientcountry')->nullable();
            $table->string('pickupclientpostalcode')->nullable();
            $table->string('notes')->nullable();
            $table->unsignedBigInteger('price')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pickuptasks');
    }
};
