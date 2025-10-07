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
            $table->enum('status', ['Scheduled', 'In Transit', 'Delivered', 'Cancelled'])->default('Scheduled');
            $table->text('notes')->nullable();
            $table->string('feedback')->nullable();

            $table->string('image_mime_type')->nullable();
            $table->string('image_filename')->nullable();
            $table->unsignedInteger('image_size')->nullable(); 

            $table->timestamps();
        });
        
        DB::statement('ALTER TABLE deliveries ADD image MEDIUMBLOB NULL');

    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
