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
        Schema::create('ambulances', function (Blueprint $table) {
            $table->id('ambulance_id'); // Primary key as ambulance_id
            $table->string('ambulance_number')->unique(); // Unique ambulance number
            $table->unsignedBigInteger('entry_by'); // User ID who created the record
            $table->unsignedBigInteger('updated_by')->nullable(); // User ID who last updated the record
            $table->timestamps();
            
            // Foreign key constraints (optional)
            $table->foreign('entry_by')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('user_id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ambulances');
    }
};
