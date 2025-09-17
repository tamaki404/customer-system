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
    Schema::create('staffs', function (Blueprint $table) {
        $table->id();
        $table->string('user_id')->unique(); 
        $table->string('staff_id')->unique();
        $table->string('supplier_id')->nullable();
        $table->string('log_id')->unique();
        $table->string('firstname');
        $table->string('lastname');
        $table->string('action_by')->nullable(); // Add this line

        $table->string('middlename')->nullable();
        $table->string('mobile_no')->nullable();
        $table->string('telephone_no')->nullable();
        $table->timestamp('email_verified_at')->nullable();
        $table->timestamps();
    });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staffs');
    }
};
