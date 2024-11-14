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
        Schema::create('mode_of_admissions', function (Blueprint $table) {
            $table->id('mode_of_admission_id'); // Primary key as mode_of_admission_id
            $table->string('mode_of_admission')->unique(); // Unique mode of admission
            $table->unsignedBigInteger('entry_by'); // User ID who created the record
            $table->unsignedBigInteger('updated_by')->nullable(); // User ID who last updated the record
            $table->boolean('status')->default(true); // Status, default to active
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
        Schema::dropIfExists('mode_of_admissions');
    }
};
