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
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id('role_id'); // Primary key as role_id
            $table->string('role_name')->unique(); // Name of the role, unique
            $table->unsignedBigInteger('entry_by')->nullable(); // User ID who created the role
            $table->unsignedBigInteger('updated_by')->nullable(); // User ID who last updated the role
            $table->boolean('status')->default(true); // Status, default to active
            $table->timestamps();
            
            // Foreign key constraints (optional)
            $table->foreign('entry_by')->references('user_id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('user_id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};
