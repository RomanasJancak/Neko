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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('eilesNumeris');
            $table->unsignedBigInteger('courrier_id')->nullable();
            $table->unsignedBigInteger('status_id');
            $table->unsignedBigInteger('clientToBill_id');         
            $table->dateTime('pickup_time_begin')->nullable();
            $table->dateTime('pickup_time_end')->nullable();
            $table->string('pickupclientname')->nullable();
            $table->string('pickupclientaddressline')->nullable();                        
            $table->string('pickupclientcity')->nullable();
            $table->string('pickupclientcountry')->nullable();
            $table->string('pickupclientpostalcode')->nullable();
            $table->unsignedBigInteger('manager_id');
            $table->string('note')->nullable();
            $table->unsignedBigInteger('price')->nullable();
            $table->unsignedBigInteger('distance')->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->bigInteger('price_adjustment_number')->default(0);
            $table->unsignedBigInteger('jobTemplate_id')->nullable();
            $table->date('date');
            $table->bigInteger('fixed_price')->default(0);
            $table->string('distance_calculation_method')->default('optimal');
            $table->foreignId('job_template_id')->nullable()->constrained()->onDelete('set null');
            $table->index('job_template_id');
            $table->foreignId('invoice_item_id')->nullable()->constrained('invoice_items')->nullOnDelete();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
