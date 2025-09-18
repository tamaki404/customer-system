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
        Schema::create('account_status', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_id');
            $table->string('staff_id')->nullable(); 
            $table->string('status_id')->nullable();
            $table->string('acc_status', 50);
            $table->text('reason_to_decline')->nullable(); 

            $table->timestamps();

        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_status');
    }
};
