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
        Schema::create('districts', function (Blueprint $table) {
            $table->id('dist_id'); // Primary key for districts
            $table->string('dist_name'); // Column for district name
            $table->unsignedBigInteger('state_id'); // Foreign key for states
            
            // Foreign key constraint
            $table->foreign('state_id')->references('state_id')->on('states')->onDelete('cascade');

            $table->timestamps(); // Created at and updated at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('districts');
    }
};
