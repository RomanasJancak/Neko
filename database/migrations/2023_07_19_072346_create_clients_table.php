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
            $table->string('client_assigned_user')->nullable();//CSV
            $table->string('balance')->nullable();//CSV
            $table->string('paid_to_date')->nullable();//CSV
            $table->string('client_currency')->nullable();//CSV
            $table->string('website')->nullable();//CSV
            $table->string('private_notes')->nullable();//CSV
            $table->string('industry')->nullable();//CSV
            $table->string('client_size')->nullable();//CSV
            $table->string('client_phone')->nullable();//CSV
            $table->string('street')->nullable();//CSV
            $table->string('apt_suite')->nullable();//CSV
            $table->string('city')->nullable();//CSV
            $table->string('state_province')->nullable();//CSV
            $table->string('postal_code')->nullable();//CSV
            $table->string('country')->nullable();//CSV
            $table->string('shipping_street')->nullable();//CSV
            $table->string('shipping_apt_suite')->nullable();//CSV
            $table->string('shipping_city')->nullable();//CSV
            $table->string('shipping_state_province')->nullable();//CSV
            $table->string('shipping_postal_code')->nullable();//CSV
            $table->string('shipping_country')->nullable();//CSV
            $table->string('client_payment_terms')->nullable();//CSV
            $table->string('id_number')->nullable();//CSV
            $table->string('public_notes')->nullable();//CSV
            $table->string('contact_phone')->nullable();//CSV
            $table->string('first_name')->nullable();//CSV
            $table->string('last_name')->nullable();//CSV
            $table->string('email')->unique();
            $table->string('custom_value_1')->nullable();//CSV
            $table->string('custom_value_2')->nullable();//CSV
            $table->string('custom_value_3')->nullable();//CSV
            $table->string('custom_value_4')->nullable();//CSV
            $table->string('contact_custom_value_1')->nullable();//CSV
            $table->string('Contact_Custom_Value_2')->nullable();//CSV
            $table->string('Contact_Custom_Value_3')->nullable();//CSV
            $table->string('Contact_Custom_Value_4')->nullable();//CSV
            $table->string('Payment_Balance')->nullable();//CSV
            $table->string('Credit_Balance')->nullable();//CSV
            $table->string('Classification')->nullable();//CSV
            //==================================
            $table->string('vat');
            $table->string('regNumber')->nullable();
            $table->string('address')->default('Default Address');
            $table->string('note')->nullable()->default('');
            $table->string('senderContacts')->nullable();
            $table->string('receiverContacts')->nullable();
            $table->string('collection_details')->nullable();
            $table->string('dropoff_details')->nullable();
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
