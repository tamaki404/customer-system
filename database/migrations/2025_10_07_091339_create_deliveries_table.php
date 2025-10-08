<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('delivery_id')->unique();  
            $table->string('order_id');              
            $table->string('supplier_id');           
            $table->date('delivery_date');     
            $table->timestamp('delivered_at')->nullable();

            $table->enum('status', ['Scheduled', 'In Transit', 'Delivered', 'Cancelled'])->default('Scheduled');
            $table->text('notes')->nullable();
            $table->string('feedback')->nullable();

            $table->timestamps();
        });
        
        DB::statement('ALTER TABLE deliveries ADD pod_file MEDIUMBLOB NULL');

    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
