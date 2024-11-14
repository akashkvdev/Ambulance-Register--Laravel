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
        Schema::create('user_details', function (Blueprint $table) {
            $table->id('user_details_id'); // Primary key as user_details_id
            $table->string('login_id', 5)->unique(); // 5-digit unique login ID
            $table->unsignedBigInteger('user_id'); // Foreign key referencing users table
            $table->string('contact_no'); // Contact number for the user
            $table->unsignedBigInteger('entry_by'); // User ID who created the record
            $table->unsignedBigInteger('updated_by'); // User ID who last updated the record
            $table->boolean('status')->default(true); // Status, default to active
            $table->timestamps();
            
            // Foreign key constraints (optional)
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('entry_by')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_details');
    }
};
