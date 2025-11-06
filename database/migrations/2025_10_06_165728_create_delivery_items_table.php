<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_items', function (Blueprint $table) {
            $table->id();
            $table->string('delivery_item_id')->unique(); 
            $table->string('customer_id');      
            $table->string('po_id');               
            $table->string('delivery_id');               
            $table->string('order_item_id');             
            $table->string('product_id');                 
            $table->string('set_id')->nullable();
            $table->decimal('planned_kilos', 10, 2)->nullable();
            $table->decimal('received_kilos', 10, 2)->nullable();
            $table->integer('planned_heads')->nullable();
            $table->integer('received_heads')->nullable();
            $table->integer('variance_heads')->nullable();
            $table->decimal('variance_kilos', 10, 2)->nullable();
            // Status (Pending, Delivered, Partial, Cancelled)
            $table->enum('status', ['Pending', 'Delivered', 'Partial', 'Cancelled'])->default('Pending');

            $table->timestamps();


        });

    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_items');
    }
};
