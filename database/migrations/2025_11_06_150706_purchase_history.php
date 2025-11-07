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
        Schema::create('purchase_histories', function (Blueprint $table) {
            $table->id();
            $table->string('customer_id')->required();
            $table->string('po_id')->required();
            $table->string('payment_id')->nullable();
            $table->string('purchase_id')->unique();
            $table->string('delivery_id');
            $table->string('label')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->enum('status', ['Pending', 'Successful', 'Rejected', 'Cancelled'])
                ->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_histories');
    }
};
