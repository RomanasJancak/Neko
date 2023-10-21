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
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('courrier_id');
            $table->dateTime('creation_time');
            $table->dateTime('completion_time');
            $table->unsignedBigInteger('status_id');
            $table->string('collection_details');
            $table->string('pickup_address');
            $table->string('delivery_address');
            $table->string('senderContacts');
            $table->unsignedBigInteger('manager_id');
            $table->string('receiverContacts');
            $table->unsignedBigInteger('group_id');
            $table->string('notes');
            $table->unsignedBigInteger('invoice_id');
            $table->foreign('client_id')->references('id')->on('clients');
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
