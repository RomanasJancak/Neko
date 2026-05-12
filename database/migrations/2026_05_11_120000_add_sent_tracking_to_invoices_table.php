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
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'status_id')) {
                $table->unsignedBigInteger('status_id')->nullable()->default(25)->after('due_date');
            }

            if (!Schema::hasColumn('invoices', 'sent_at')) {
                $table->timestamp('sent_at')->nullable()->after('status_id');
            }

            if (!Schema::hasColumn('invoices', 'sent_by')) {
                $table->unsignedBigInteger('sent_by')->nullable()->after('sent_at');
                $table->foreign('sent_by')->references('id')->on('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'sent_by')) {
                $table->dropForeign(['sent_by']);
                $table->dropColumn('sent_by');
            }

            if (Schema::hasColumn('invoices', 'sent_at')) {
                $table->dropColumn('sent_at');
            }

            if (Schema::hasColumn('invoices', 'status_id')) {
                $table->dropColumn('status_id');
            }
        });
    }
};
