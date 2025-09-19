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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_id')->unique(); 
            $table->string('order_id');
            $table->string('supplier_id');
            $table->string('status')->default('Pending')->nullable();
            $table->string('image_mime_type')->nullable();
            $table->string('image_filename')->nullable();
            $table->unsignedInteger('image_size')->nullable(); 
            $table->timestamps();
        });

        DB::statement('ALTER TABLE purchase_orders ADD image MEDIUMBLOB NULL');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
