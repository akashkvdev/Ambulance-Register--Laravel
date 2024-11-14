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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id('organization_id');
            $table->string('organization_name')->unique();
            $table->string('org_short_name')->unique();
            $table->string('organization_location')->nullable();
            $table->boolean('status')->default(true);
            $table->unsignedBigInteger('entry_by')->nullable();
            $table->timestamps();

            // Foreign key constraint (if needed)
            $table->foreign('entry_by')->references('user_id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
