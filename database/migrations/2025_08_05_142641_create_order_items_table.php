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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id(); // This creates bigint UNSIGNED AUTO_INCREMENT
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id'); // Matches your products table ID type
            $table->integer('quantity'); // Matches your products quantity column type
            $table->decimal('unit_price', 10, 2); // Matches your products price column type
            $table->decimal('total_price', 10, 2);
            $table->timestamps(); // This creates created_at and updated_at timestamp columns
            
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};