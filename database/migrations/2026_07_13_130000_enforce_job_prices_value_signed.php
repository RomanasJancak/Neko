<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $column = DB::selectOne("SHOW COLUMNS FROM `job_prices` LIKE 'value'");
        $type = strtolower((string) ($column->Type ?? ''));

        if (str_contains($type, 'unsigned')) {
            DB::statement('ALTER TABLE job_prices MODIFY COLUMN value BIGINT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $column = DB::selectOne("SHOW COLUMNS FROM `job_prices` LIKE 'value'");
        $type = strtolower((string) ($column->Type ?? ''));

        if ($type !== '' && !str_contains($type, 'unsigned')) {
            DB::statement('ALTER TABLE job_prices MODIFY COLUMN value BIGINT UNSIGNED NULL');
        }
    }
};
