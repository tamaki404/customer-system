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
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('receipt_image')->nullable();
            $table->date('purchase_date');
            $table->string('store_name');
            $table->decimal('total_amount', 10, 2); 
            $table->string('invoice_number');
            $table->text('notes')->nullable();
            $table->string('status')->default('Pending');
            $table->string('verified_by')->nullable();
            $table->date('verified_at')->nullable();
            $table->bigInteger(column: 'receipt_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
