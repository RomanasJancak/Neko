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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->string('number')->nullable();//CSV
            $table->string('client_user')->nullable();//CSV
            $table->string('balance')->nullable();//CSV
            $table->string('paid_to_date')->nullable();//CSV
            $table->string('client_currency')->nullable();//CSV
            $table->string('website')->nullable();//CSV
            $table->string('private_notes')->nullable();//CSV
            $table->string('phone')->nullable();//CSV
            $table->string('address_line')->nullable();
            $table->string('postal_code')->nullable();//CSV
            $table->string('city')->nullable();//CSV
            $table->string('country')->nullable();       
            $table->string('pickup_adress_line')->nullable();//CSV
            $table->string('pickup_postal_code')->nullable();//CSV
            $table->string('pickup_city')->nullable();//CSV
            $table->string('pickup_country')->nullable();//CSV
            $table->string('dropoff_adress_line')->nullable();//CSV
            $table->string('dropoff_postal_code')->nullable();//CSV
            $table->string('dropoff_city')->nullable();//CSV
            $table->string('dropoff_country')->nullable();//CSV
            // CLEANUP 2026-05-09: unused by application code
            // $table->string('public_notes')->nullable();//CSV
            // CLEANUP 2026-05-09: unused by application code
            // $table->string('contact_phone')->nullable();//CSV
            // CLEANUP 2026-05-09: unused by application code
            // $table->string('first_name')->nullable();//CSV
            // CLEANUP 2026-05-09: unused by application code
            // $table->string('last_name')->nullable();//CSV
            $table->string('email')->nullable();
            $table->string('Credit_Balance')->nullable();//CSV
            //==================================
            $table->string('vat')->nullable();
            $table->string('address')->default('Default Address');
            $table->string('note')->nullable()->default('');
            // $table->unsignedBigInteger('contactPersonForPickup')->default('0')->nullable();
            $table->string('receiverContacts')->nullable();
            $table->string('collection_details')->nullable();
            $table->string('dropoff_details')->nullable();
            $table->string('shortenedName')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
