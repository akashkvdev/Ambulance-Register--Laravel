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
        Schema::create('assign_navigations', function (Blueprint $table) {
            $table->id('assign_navigations_id');
            $table->unsignedBigInteger('nav_id');
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('entry_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();


              // $table->foreign('entry_by')->references('id')->on('users')->onDelete('cascade');
              $table->foreign('nav_id')->references('nav_id')->on('navigations')->onDelete('cascade');
              $table->foreign('role_id')->references('role_id')->on('user_roles')->onDelete('cascade');
              $table->foreign('updated_by')->references('user_id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assign_navigations');
    }
};
