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
        Schema::create('job_templates', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('pickup_address_id')->nullable();
            $table->dateTime('pickup_time_begin')->nullable();
            $table->dateTime('pickup_time_end')->nullable();
            $table->json('template_data')->nullable(); // Stores pickup, dropoffs, return data
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_templates');
    }
};
