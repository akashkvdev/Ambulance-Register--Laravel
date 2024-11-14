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
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id'); // Primary key as user_id
            $table->string('user_name'); // Name of the user
            $table->enum('gender', ['male', 'female', 'other'])->nullable(); // Gender field
           $table->boolean('user_type')->default(true); // Type of user (e.g., 'Temporary' means false i.e 0, 'Permanent is true i.e 1')
            // $table->string('contact_no'); // Contact No 
            $table->unsignedBigInteger('entry_by'); // User ID who created the record
            $table->unsignedBigInteger('updated_by')->nullable(); // User ID who last updated the record
            $table->unsignedBigInteger('organization_id'); // Foreign key to the organizations table
            $table->unsignedBigInteger('role_id')->nullable(); // Foreign key to the roles table
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            // $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            
            // Foreign key constraints (optional)
            // $table->foreign('entry_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('user_id')->on('users')->onDelete('set null');
            $table->foreign('organization_id')->references('organization_id')->on('organizations')->onDelete('cascade');
            // $table->foreign('role_id')->references('role_id')->on('user_roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
