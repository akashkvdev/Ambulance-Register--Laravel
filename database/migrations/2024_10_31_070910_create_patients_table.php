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
        Schema::create('patients', function (Blueprint $table) {
            $table->id('patient_id'); // Primary key for patients
            $table->string('patient_name'); // Column for patient name
            $table->string('adhar_no')->unique(); // Column for Aadhar number (unique)
            $table->string('other_id_type'); // Column for other ID type
            $table->string('other_id_no')->unique(); // Column for other ID number
            $table->string('mobile_number'); // Column for mobile number
            $table->unsignedBigInteger('entry_by'); // Column for the user who created the entry
            $table->unsignedBigInteger('updated_by')->nullable(); // Column for the user who last updated the entry
            $table->timestamps(); // Created at and updated at columns
            
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
        Schema::dropIfExists('patients');
    }
};
