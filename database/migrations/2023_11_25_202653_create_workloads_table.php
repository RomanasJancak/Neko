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
        Schema::create('workloads', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('capacity');
            $table->timestamp('date');
            $table->unsignedBigInteger('day_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('bike_id');
            $table->foreign('day_id')->references('id')->on('days');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('bike_id')->references('id')->on('bikes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workloads');
    }
};
