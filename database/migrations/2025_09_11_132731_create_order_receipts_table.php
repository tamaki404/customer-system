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
        Schema::create('order_receipts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('or_id')->nullable();
            $table->string('po_id')->nullable();
            $table->string('feedback')->nullable();
            $table->string('report_subject')->nullable();
            $table->string('label')->nullable();
            $table->string('status')->nullable();
            $table->string('received_at')->nullable();
            $table->timestamp('reported_at');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_receipts');
    }
};
