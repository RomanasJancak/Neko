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

            $table->unsignedBigInteger('clientToBill_id');
            $table->unsignedBigInteger('status_id');
            $table->string('notes')->nullable();
            $table->unsignedBigInteger('price')->nullable();
            $table->unsignedBigInteger('distance')->nullable();
            $table->bigInteger('price_adjustment_number')->default(0);
            $table->bigInteger('fixedPrice')->default(0);
            $table->date('date');

            $table->json('pickuptask_data');
            $table->json('dropOffs_data');
            $table->json('return_data');
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
