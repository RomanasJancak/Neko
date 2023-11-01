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
            $table->string('email')->unique();
            $table->string('vat');
            $table->string('regNumber')->nullable();
            $table->string('address');
            $table->string('note');
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
