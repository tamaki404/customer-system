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
        Schema::table('purchase_orders', function (Blueprint $table) {
            // Remove image-related columns
            $table->dropColumn(['image', 'image_mime_type', 'image_filename', 'image_size']);
            
            // Add new columns for the new workflow
            $table->text('notes')->nullable();
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->string('staff_id')->nullable(); // Staff who confirmed the order
            $table->timestamp('confirmed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            // Add back image columns
            $table->string('image_mime_type')->nullable();
            $table->string('image_filename')->nullable();
            $table->unsignedInteger('image_size')->nullable();
            $table->mediumBlob('image')->nullable();
            
            // Remove new columns
            $table->dropColumn(['notes', 'total_amount', 'staff_id', 'confirmed_at']);
        });
    }
};
