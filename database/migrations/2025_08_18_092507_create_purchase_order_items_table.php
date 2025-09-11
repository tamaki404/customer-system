<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrderItemsTable extends Migration
{
    public function up()
    {
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->string('po_id');
            $table->string('product_id');
            $table->integer('quantity');
            $table->decimal('unit_price', 8, 2);
            $table->decimal('total_price', 10, 2);
            $table->timestamps();

            // Add foreign key constraints separately
            // $table->foreign('po_id')->references('id')->on('purchase_orders')->onDelete('cascade');
            // $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            $table->index(['product_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_order_items');
    }
}
