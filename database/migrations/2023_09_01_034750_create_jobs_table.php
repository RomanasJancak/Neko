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
            $table->string('pickupClientName');
            $table->string('pickupclientaddressline')->nullable();
            $table->string('collection_details')->nullable();
            
            $table->unsignedBigInteger('pickup_adress_postalCode')->nullable();
            $table->unsignedBigInteger('pickup_adress_street')->nullable();
            $table->string('delivery_address');
            $table->unsignedBigInteger('delivery_adress_postalCode_id')->nullable();
            $table->string('senderContacts');
            $table->unsignedBigInteger('manager_id');
            $table->string('receiverContacts');
            $table->string('notes')->nullable();
            $table->unsignedBigInteger('price')->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable();
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
