<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAmbulanceManageIdToPaymentsTable extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('ambulace_manage_id')->nullable(); // Add ambulance_manage_id column
            
            // Add foreign key constraint
            $table->foreign('ambulace_manage_id')->references('ambulace_manage_id')->on('ambulance_manages')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Drop foreign key constraint and column
            $table->dropForeign(['ambulace_manage_id']);
            $table->dropColumn('ambulace_manage_id'); // Remove the column if the migration is rolled back
        });
    }
}
