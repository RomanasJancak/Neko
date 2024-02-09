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
        Schema::create('add_on_rules', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->dateTime('begin_date');
            $table->dateTime('end_date');
            $table->string('name');//code
            $table->string('display_name');
            $table->unsignedBigInteger('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('add_on_rules');
    }
};
