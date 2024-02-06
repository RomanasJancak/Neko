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
        Schema::create('add_ons', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('model_type');// either app/models/Job or app/models/Package or app/models/PackageType
            $table->unsignedBigInteger('model_id');
            $table->dateTime('begin_date');
            $table->dateTime('end_date');
            $table->string('name');//temp
            $table->string('display_name');
            $table->unsignedBigInteger('price');//temp

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('add_ons');
    }
};
