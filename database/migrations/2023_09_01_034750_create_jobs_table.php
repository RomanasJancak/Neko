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
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('receiver_id');
            $table->unsignedBigInteger('courrier_id')->nullable();
            $table->dateTime('pickup_time_begin');
            $table->dateTime('pickup_time_end');
            $table->dateTime('dropoff_time_begin');
            $table->dateTime('dropoff_time_end');
            $table->unsignedBigInteger('status_id');
            $table->string('collection_details')->nullable();
            $table->string('dropoff_details')->nullable();
            $table->string('pickup_address');
            $table->string('delivery_address');
            $table->string('senderContacts');
            $table->unsignedBigInteger('manager_id');
            $table->string('receiverContacts');
            $table->string('notes')->nullable();
            $table->foreign('sender_id')->references('id')->on('clients');
            $table->foreign('receiver_id')->references('id')->on('clients');
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
