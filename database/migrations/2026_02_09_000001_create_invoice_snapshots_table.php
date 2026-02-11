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
        Schema::create('invoice_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->dateTime('generated_at');
            $table->json('data');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->unique(['invoice_id', 'version']);
            $table->index(['invoice_id', 'generated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_snapshots');
    }
};
