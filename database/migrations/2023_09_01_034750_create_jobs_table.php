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
            $table->dateTime('pickup_time_begin');
            $table->dateTime('pickup_time_end');
            $table->string('pickupclientname');
            $table->string('pickupclientaddressline');                        
            $table->string('pickupclientcity');
            $table->string('pickupclientcountry');
            $table->string('pickupclientpostalcode');
            $table->unsignedBigInteger('manager_id');
            $table->string('notes')->nullable();
            $table->unsignedBigInteger('price')->nullable();
            $table->unsignedBigInteger('distance')->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->bigInteger('price_adjustment_number')->default(0);
            $table->foreign('clientToBill_id')->references('id')->on('clients');
            $table->foreign('manager_id')->references('id')->on('users');
            $table->foreign('courrier_id')->references('id')->on('users');
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
