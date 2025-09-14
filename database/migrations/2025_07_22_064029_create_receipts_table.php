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
            $table->string('receipt_id')->unique();
            $table->timestamps();
            $table->date('purchase_date');
            $table->string('store_name');
            $table->decimal('total_amount', 10, 2); 
            $table->string('invoice_number')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('Pending');
            $table->string('receipt_number')->nullable();
            $table->string('id');
            $table->string('po_id');
            $table->string('action_by')->nullable();
            $table->date('action_at')->nullable();
            $table->string('rejected_note')->nullable();
            $table->string('additional_note')->nullable();
            $table->timestamp('verified_at')->nullable()->change();

        });
        DB::statement('ALTER TABLE receipts ADD receipt_image MEDIUMBLOB NULL');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
