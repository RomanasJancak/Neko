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
        Schema::create('approved_postal_code_area_client', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('client_id')->constrained()->onDelete('cascade'); // Foreign key to clients table
            $table->foreignId('approved_postal_code_area_id')->constrained()->onDelete('cascade'); // Foreign key to approved_postal_code_areas table
            $table->timestamps();

            $table->unique(['client_id', 'approved_postal_code_area_id']); // Ensure unique combinations
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approved_postal_code_area_client');
    }
};
