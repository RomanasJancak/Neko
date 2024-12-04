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
        Schema::create('distances', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('origin_address')->nullable();
            $table->string('destination_address')->nullable();
            $table->string('origin_address_id')->nullable();
            $table->string('destination_address_id')->nullable();
            $table->double('origin_lat')->nullable();
            $table->double('origin_lng')->nullable();
            $table->double('destination_lat')->nullable();
            $table->double('destination_lng')->nullable();
            $table->string('mode_of_travel');
            $table->unsignedBigInteger('distance')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distances');
    }
};
