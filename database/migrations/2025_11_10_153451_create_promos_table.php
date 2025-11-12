<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->string('promo_id')->unique()->required();
            $table->string('name', 100);
            $table->string('description', 255)->nullable();
            $table->decimal('value', 10, 2);
            $table->enum('value_type', ['Percentage', 'Fixed']);
            $table->enum('category', ['Wholesale', 'Distributor', 'HRI', 'Dealer']);
            $table->string('product_id')->required();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->integer('quantity')->default(0)->required();
            $table->enum('status', ['Active', 'Sold out', 'Ended', 'Inactive', 'Cancelled'])->default('Active');
            $table->string('user_id')->required();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};
