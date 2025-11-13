<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->string('po_id')->unique();
            $table->enum('status', ['Pending', 'Accepted', 'Progressing', 'Completed', 'Cancelled', 'Rejected'])->default('Pending')->required();
            $table->string('user_id', 30)->required(); 
            $table->decimal('total_amount', 12, 2)->default(0)->nullable();
            $table->string('notes', 255)->nullable();
            $table->string('action_by', 50)->required();
            $table->timestamp('action_at')->required();
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
