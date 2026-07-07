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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->dateTime('date');
            $table->unsignedBigInteger('status_id');
            $table->unsignedBigInteger('job_id');
            $table->unsignedBigInteger('order_number');
            $table->string('note')->nullable();
            $table->string('taskable_type')->nullable();
            $table->unsignedBigInteger('taskable_id')->nullable();

            $table->index(['taskable_type', 'taskable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
