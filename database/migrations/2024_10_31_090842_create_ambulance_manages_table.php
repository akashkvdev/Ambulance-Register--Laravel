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
        Schema::create('ambulance_manages', function (Blueprint $table) {
            $table->id('ambulace_manage_id'); // Primary key for ambulance management
            $table->date('date_of_visit'); // Column for the date of visit
            $table->time('arrival_time'); // Column for the arrival time
            $table->unsignedBigInteger('hospitality_id')->nullable(); // Foreign key for hospitality
            $table->unsignedBigInteger('patient_id'); // Foreign key for patients
            $table->unsignedBigInteger('location_id'); // Foreign key for locations
            $table->unsignedBigInteger('ambulance_id'); // Foreign key for ambulances
            $table->unsignedBigInteger('entry_by'); // Column for the user who created the entry
            $table->unsignedBigInteger('updated_by')->nullable(); // Column for the user who last updated the entry
            $table->unsignedBigInteger('payment_id'); // Foreign key for payment

            $table->timestamps(); // Created at and updated at columns

            // Foreign key constraints
            $table->foreign('hospitality_id')->references('hospitality_id')->on('hospitalities')->onDelete('set null');
            $table->foreign('patient_id')->references('patient_id')->on('patients')->onDelete('cascade');
            $table->foreign('location_id')->references('location_id')->on('locations')->onDelete('cascade');
            $table->foreign('ambulance_id')->references('ambulance_id')->on('ambulances')->onDelete('cascade');
            $table->foreign('entry_by')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('user_id')->on('users')->onDelete('set null');
            $table->foreign('payment_id')->references('payment_id')->on('payments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ambulance_manages');
    }
};
