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
        Schema::create('product_sales', function (Blueprint $table) {
            $table->id();
            $table->string('sale_id')->required(); 
            $table->string('customer_id')->required(); 
            $table->string('set_id')->required(); 
            $table->string('status')->nullable(); 
            $table->decimal('sale_price', 10, 2);
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->string('action_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_sales');
    }
};
