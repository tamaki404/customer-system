<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_requests', function (Blueprint $table) {
            $table->id();
            $table->string('po_id');  
            $table->string('delivery_id');              
            $table->string('customer_id');           
            $table->date('delivery_date');     
            $table->timestamp('delivered_date')->nullable();
            $table->date('due_date')->nullable()->after('delivered_date');
            $table->decimal('sum_balance', 15, 2);
            $table->enum('status', ['Scheduled', 'Delivered', 'Cancelled', 'Pending', 'Rejected'])->default('Pending');
            $table->enum('return_status', ['Unresolved', 'Resolved', 'Resolved with balance'])->default('Unresolved');
            $table->string('action_by', 255)->required();
            $table->string('action_at', 255)->required();
            $table->string('feedback')->nullable();
            $table->timestamps();
        });
        DB::statement('ALTER TABLE delivery_requests ADD pod_file MEDIUMBLOB NULL');

        Schema::create('delivery_item_requests', function (Blueprint $table) {
            $table->id();
            $table->string('delivery_id'); 
            $table->string('delivery_item_id')->unique(); 
            $table->string('customer_id');                         
            $table->string('product_id');                 
            $table->string('set_id')->nullable();
            $table->decimal('planned_kilos', 10, 2)->nullable();
            $table->decimal('received_kilos', 10, 2)->nullable();
            $table->integer('planned_heads')->nullable();
            $table->integer('received_heads')->nullable();
            $table->decimal('balance', 15, 2);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_requests');
    }
};
