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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('customer_id')->required(); 

            $table->string('user_id');
            $table->string('home_street');
            $table->string('home_subdivision');
            $table->string('home_barangay');
            $table->string('home_city');
            $table->string('office_street');
            $table->string('office_subdivision');
            $table->string('office_barangay');
            $table->string('office_city');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
