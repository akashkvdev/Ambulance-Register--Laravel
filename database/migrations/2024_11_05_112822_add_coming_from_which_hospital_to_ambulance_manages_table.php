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
        Schema::table('ambulance_manages', function (Blueprint $table) {
            $table->string('coming_from_which_hospital')->nullable()->after('arrival_time'); // Replace 'column_name' with the column after which you want to add this new field
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ambulance_manages', function (Blueprint $table) {
            //
        });
    }
};
