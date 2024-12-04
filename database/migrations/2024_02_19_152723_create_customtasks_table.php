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
        Schema::create('customtasks', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('status_id');
            $table->unsignedBigInteger('address_id')->nullable();
            $table->string('name')->nullable();
            $table->string('adress_line')->nullable();//CSV
            $table->string('postal_code')->nullable();//CSV
            $table->string('city')->nullable();//CSV
            $table->string('country')->nullable();//CSV         
            $table->dateTime('time_begin');
            $table->dateTime('time_end');
            $table->bigInteger('price')->nullable();
            $table->string('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customtasks');
    }
};
