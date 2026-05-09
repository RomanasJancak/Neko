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
            $table->string('name')->nullable();
            $table->unsignedBigInteger('clientToBill_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->unsignedBigInteger('pickup_address_id')->nullable();
            $table->dateTime('pickup_time_begin')->nullable();
            $table->dateTime('pickup_time_end')->nullable();
            $table->json('template_data')->nullable();
            $table->string('notes')->nullable();
            $table->unsignedBigInteger('price')->nullable();
            $table->unsignedBigInteger('distance')->nullable();
            $table->bigInteger('price_adjustment_number')->default(0);
            $table->bigInteger('fixedPrice')->default(0);
            $table->date('date')->nullable();
            $table->json('pickuptask_data')->nullable();
            $table->json('dropOffs_data')->nullable();
            $table->json('return_data')->nullable();
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
