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
            $table->string('user_id', 26);
            $table->string('po_id')->unique();
            $table->string('reference_code')->nullable();
            $table->string('receiver_name');
            $table->string('receiver_mobile', 15);
            $table->string('postal_code', 10)->nullable();
            $table->unsignedBigInteger('region_id');
            $table->unsignedBigInteger('province_id');
            $table->unsignedBigInteger('municipality_id');
            $table->unsignedBigInteger('barangay_id');
            $table->string('street'); 
            $table->string('company_name')->nullable();
            $table->text('billing_address');
            $table->string('contact_phone');
            $table->string('contact_email');
            $table->text('order_notes')->nullable();
            $table->string('po_attachment')->nullable(); 
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2);
            $table->string('status');
            $table->timestamp('order_date');

            $table->timestamp('approved_at')->nullable();
            $table->string('approved_by')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->string('rejected_by')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancelled_by')->nullable();
            $table->string('cancelled_user_type')->nullable();

            $table->string('payment_status',  255)->nullable();
            $table->string('payment_notes',  255)->nullable();
            $table->string('payment_reject_details',  255)->nullable();
            $table->timestamp('payment_at',)->nullable();


            
            $table->timestamps();


            $table->index(['user_id', 'status']);
            $table->index('order_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_orders');
    }
}

