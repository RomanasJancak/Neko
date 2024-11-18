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
        Schema::create('add_on_rule_package_type', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('add_on_rule_id');
            $table->unsignedBigInteger('package_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('add_on_rule_package_type');
    }
};
