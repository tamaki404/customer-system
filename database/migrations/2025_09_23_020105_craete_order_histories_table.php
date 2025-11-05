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
        Schema::create('order_histories', function (Blueprint $table) {
            $table->id();
            $table->string('order_id');   
            $table->string('delivery_id')->nullable();   
            $table->string('receipt_id')->nullable();  
            $table->string('action_by'); 
            $table->string('history_id')->unique();  
            $table->string('status');
            $table->string('label');
            $table->string('amount');
            $table->timestamp('action_at')->useCurrent(); 
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
