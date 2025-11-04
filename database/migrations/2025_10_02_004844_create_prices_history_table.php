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
        Schema::create('price_histories', function (Blueprint $table) {
            $table->id();
            $table->string('phistory_id')->required(); 
            $table->string('customer_id')->required(); 
            $table->string('set_id')->required(); 
            $table->string('sale_id')->nullable(); 
            $table->string('action_by')->required(); 
            $table->string('action')->required(); 


            $table->decimal('new_price', 10, 2);
            $table->decimal('past_price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_histories');
    }
};
