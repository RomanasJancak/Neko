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
        Schema::create('locked_fields', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('field_name');
            $table->boolean('is_locked')->default(true);
            $table->string('model')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locked_fields');
    }
};
