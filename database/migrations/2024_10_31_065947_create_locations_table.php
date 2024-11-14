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
        Schema::create('locations', function (Blueprint $table) {
            $table->id('location_id'); // Primary key for locations
            $table->string('location'); // Column for location name
            $table->unsignedBigInteger('dist_id'); // Foreign key for districts
            $table->unsignedBigInteger('entry_by'); // Column for the user who created the entry
            $table->unsignedBigInteger('updated_by')->nullable(); // Column for the user who last updated the entry
            $table->timestamps(); // Created at and updated at columns
            
            // Foreign key constraint
            $table->foreign('dist_id')->references('dist_id')->on('districts')->onDelete('cascade');
            // Optional: If you have a users table for entry_by and updated_by, you can add those constraints as well
            $table->foreign('entry_by')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('user_id')->on('users')->onDelete('set null');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
