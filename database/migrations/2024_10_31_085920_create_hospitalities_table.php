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
        Schema::create('hospitalities', function (Blueprint $table) {
            $table->id('hospitality_id'); // Primary key for hospitality
            $table->unsignedBigInteger('mode_of_admission_id'); // Foreign key for mode of admission
            $table->string('admitted_area'); // Column for admitted area
            $table->string('referred_by')->nullable(); // Column for referred by, nullable
            $table->text('remarks')->nullable(); // Column for remarks, nullable
            $table->boolean('ip')->default(false); // Column for inpatient status
            $table->boolean('op')->default(false); // Column for outpatient status
            $table->unsignedBigInteger('patient_id'); // Foreign key for patients
            $table->unsignedBigInteger('entry_by'); // Column for the user who created the entry
            $table->unsignedBigInteger('updated_by')->nullable(); // Column for the user who last updated the entry
            $table->unsignedBigInteger('department_id'); // Foreign key for department
            $table->timestamps(); // Created at and updated at columns

            // Foreign key constraints
            $table->foreign('mode_of_admission_id')->references('mode_of_admission_id')->on('mode_of_admissions')->onDelete('cascade');
            $table->foreign('patient_id')->references('patient_id')->on('patients')->onDelete('cascade');
            $table->foreign('entry_by')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('user_id')->on('users')->onDelete('set null');
            $table->foreign('department_id')->references('department_id')->on('departments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospitalities');
    }
};
