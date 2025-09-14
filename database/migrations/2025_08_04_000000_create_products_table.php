<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('image_mime')->nullable();
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->string('status')->nullable();
            $table->string('category')->nullable();
            $table->timestamps();
            $table->string('product_id')->nullable();
            $table->string('unit')->nullable();
        });
        DB::statement('ALTER TABLE products ADD image MEDIUMBLOB NULL');

    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
