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
        Schema::create('navigations', function (Blueprint $table) {
            $table->id('nav_id'); // Primary key as nav_id
            $table->string('nav_name')->unique(); // Unique navigation name
            $table->string('nav_url'); // URL for the navigation link
            $table->string('nav_icon')->nullable(); // Icon associated with the navigation (nullable)
            $table->unsignedBigInteger('entry_by'); // User ID who created the navigation
            $table->unsignedBigInteger('updated_by'); // User ID who last updated the navigation
            $table->boolean('status')->default(true); // Status, default to active
            $table->timestamps();
            
            // Foreign key constraints (optional)
            $table->foreign('entry_by')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('user_id')->on('users')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('navigations');
    }
};
