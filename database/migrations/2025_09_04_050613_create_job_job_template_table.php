<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Intentionally left blank.
        // CLEANUP NOTE: The live schema (current_structure.sql) does not include job_job_template.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op because up() does not create a table.
    }
};
