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
        Schema::create('role_hierarchy', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_role_id');
            $table->unsignedBigInteger('child_role_id');

            $table->primary(['parent_role_id', 'child_role_id']);

            $table->foreign('parent_role_id')
                ->references('id')->on('roles')
                ->onDelete('cascade');

            $table->foreign('child_role_id')
                ->references('id')->on('roles')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_hierarchy');
    }
};
