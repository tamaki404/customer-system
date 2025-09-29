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
        Schema::create('product_requirements', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('product_id'); 
            $table->string('condition')->nullable();
            $table->string('weight_requirement')->nullable();
            $table->string('primary_packaging');
            $table->string('secondary_packaging');
            $table->string('labeling_requirement')->nullable();
            $table->string('rejection_parameter')->nullable();
            $table->string('supplier_id')->required(); 

            $table->timestamps();
        });

        Schema::create('delivery_requirements', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('ppe_requirements')->nullable(); 
            $table->string('delivery_frequency')->nullable();
            $table->string('delivery_address_1')->nullable();
            $table->string('delivery_address_2')->nullable();
            $table->string('delivery_address_3')->nullable();
            $table->string('delivery_instructions')->nullable();
            $table->string('supplier_id')->required(); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_requirements');
    }
};
