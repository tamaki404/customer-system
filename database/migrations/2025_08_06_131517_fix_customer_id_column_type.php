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
        Schema::table('orders', function (Blueprint $table) {
            // Change customer_id from bigint to string to accommodate UUIDs/ULIDs
            $table->string('customer_id', 26)->change(); // 26 chars for ULID, 36 for UUID
            
            // If you also have string product_ids, uncomment this:
            // $table->string('product_id', 26)->change();
            
            // Update the index
            $table->dropIndex(['customer_id']); // Drop old index
            $table->index('customer_id'); // Recreate index
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Revert back to bigint (only if you're sure no string IDs exist)
            $table->unsignedBigInteger('customer_id')->change();
        });
    }
};