<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDriverDetailsToAmbulanceManagesTable extends Migration
{
    public function up(): void
    {
        Schema::table('ambulance_manages', function (Blueprint $table) {
            $table->string('driver_name'); // Add driver_name column
            $table->string('driver_contact_no'); // Add driver_contact_no column
        });
    }

    public function down(): void
    {
        Schema::table('ambulance_manages', function (Blueprint $table) {
            $table->dropColumn(['driver_name', 'driver_contact_no']); // Remove the columns if the migration is rolled back
        });
    }
}
