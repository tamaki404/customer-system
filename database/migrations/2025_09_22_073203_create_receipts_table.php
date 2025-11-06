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
            $table->string('receipt_id')->unique(); 
            $table->string('po_id');
            $table->string('customer_id');
            $table->string('order_id');
            $table->enum('status', ['Pending', 'Verified', 'Rejected'])
                ->default('Pending');
            $table->decimal('total_amount', 12, 2)->default(0)->nullable();
            $table->string('reason')->nullable();
            $table->string('image_mime_type')->nullable();
            $table->string('image_filename')->nullable();
            $table->unsignedInteger('image_size')->nullable(); 
            $table->string('action_by')->nullable();
            $table->timestamp('action_at')->nullable();

            $table->timestamps();
        });
        DB::statement('ALTER TABLE receipts ADD image MEDIUMBLOB NULL');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
