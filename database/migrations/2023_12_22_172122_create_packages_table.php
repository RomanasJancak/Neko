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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('job_id')->nullable();
            $table->unsignedBigInteger('task_id')->nullable(); 
            $table->unsignedBigInteger('status_id')->nullable(); 
            $table->unsignedBigInteger('packageType_id');
            $table->unsignedBigInteger('orderNumber')->nullable();     
            $table->string('weight');
            $table->string('dimensions');
            $table->string('quantity');
            $table->unsignedBigInteger('address_id')->nullable();
            $table->string('dropoff_adress_line')->nullable();//CSV
            $table->string('dropoff_postal_code')->nullable();//CSV
            $table->string('dropoff_city')->nullable();//CSV
            $table->string('dropoff_country')->nullable();//CSV
            $table->string('dropoff_name')->nullable();
            $table->dateTime('packagedropofftimebegin');
            $table->dateTime('packagedropofftimeend');
            $table->string('name')->nullable();
            $table->bigInteger('price')->nullable();
            $table->unsignedBigInteger('baseQuantityThreshold')->nullable();
            $table->unsignedBigInteger('maxQuantityThreshold')->nullable();
            $table->string('notes')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
