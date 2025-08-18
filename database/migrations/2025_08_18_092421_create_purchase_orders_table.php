<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('po_number')->unique();
            $table->string('reference_code')->nullable();
            $table->string('receiver_name');
            $table->text('shipping_address');
            $table->text('billing_address');
            $table->string('contact_phone');
            $table->string('contact_email');
            $table->text('order_notes')->nullable();
            $table->string('po_attachment')->nullable(); // file path
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2);
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled', 'delivered'])->default('pending');
            $table->timestamp('order_date');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->timestamps();

            // Add foreign key constraints separately
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            // $table->foreign('rejected_by')->references('id')->on('users')->onDelete('set null');

            $table->index(['user_id', 'status']);
            $table->index('order_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_orders');
    }
}

