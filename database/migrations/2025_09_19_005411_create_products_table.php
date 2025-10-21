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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            // $table->string('name');
            // $table->decimal('base_price', 10, 2);
            // $table->string('category');
            // $table->string('measurement_type');
            // $table->string('unit')->nullable();
            // $table->string('weight')->nullable();
            // $table->string('added_by')->nullable();
            // $table->string('status');

            $table->string('product_id')->unique();
            $table->string('name')->required();
            $table->decimal('base_price', 10, 2)->nullable();
            $table->enum('category', ['By products', 'Cut ups', 'Fillets', 'Dressed chickens', 'Uncategorized'])->required();
            $table->enum('measurement_type', ['Heads', 'Kilos', 'Heads&Kilos'])->required();
            $table->string('added_by')->required();
            $table->string('status')->default('Listed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
