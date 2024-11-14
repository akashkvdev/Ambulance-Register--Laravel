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
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id'); // Primary key for payments
            $table->decimal('payment_amount', 10, 2); // Column for payment amount
            $table->unsignedBigInteger('ambulance_id'); // Foreign key for ambulance
            $table->unsignedBigInteger('entry_by'); // Column for the user who created the entry
            $table->unsignedBigInteger('updated_by'); // Column for the user who last updated the entry
            $table->timestamps(); // Created at and updated at columns
            
            // Foreign key constraint
            $table->foreign('ambulance_id')->references('ambulance_id')->on('ambulances')->onDelete('cascade');
            $table->foreign('entry_by')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
