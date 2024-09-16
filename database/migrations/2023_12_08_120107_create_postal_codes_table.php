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
        Schema::create('postal_codes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name')->nullable();
            // Postal code components
            $table->string('postal_code', 8)->unique(); // Full postal code with a max length of 8 characters
            $table->string('outward_code', 4); // Outward part of the postal code
            $table->string('inward_code', 4); // Inward part of the postal code

            // Optional additional fields
            $table->string('area')->nullable(); // Postal area (e.g., first one or two letters of outward code)
            $table->string('district')->nullable(); // Postal district (the rest of the outward code)
            $table->string('sector')->nullable(); // Postal sector (first digit of inward code)
            $table->string('unit')->nullable(); // Postal unit (last two characters of inward code)
            
                       
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postal_codes');
    }
};
