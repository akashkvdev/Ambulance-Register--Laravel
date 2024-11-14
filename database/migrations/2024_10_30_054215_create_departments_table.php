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
        Schema::create('departments', function (Blueprint $table) {
            $table->id('department_id'); // Primary key as department_id
            $table->string('department_name')->unique(); // Unique department name
            $table->unsignedBigInteger('entry_by'); // User ID who created the department
            $table->unsignedBigInteger('updated_by')->nullable(); // User ID who last updated the department
            $table->unsignedBigInteger('organization_id'); // User ID who last updated the department
            $table->boolean('status')->default(true); // Status, default to active
            $table->timestamps();
            
            // Foreign key constraints (optional)
            $table->foreign('entry_by')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('organization_id')->references('organization_id')->on('organizations')->onDelete('cascade');
            $table->foreign('updated_by')->references('user_id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
