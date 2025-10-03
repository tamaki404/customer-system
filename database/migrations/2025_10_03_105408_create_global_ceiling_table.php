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
        Schema::create('global_ceilings', function (Blueprint $table) {
            $table->id();
            $table->string('ceiling_id')->required();
            $table->decimal('fixed_price', 10, 2)->nullable();
            $table->decimal('percentage_ceiling', 5, 2)->nullable();
            $table->enum('method', ['Fixed', 'Percentage'])->default('Fixed');
            $table->string('city_selected')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->string('staff_id')->nullable();
            $table->timestamps();
        });


        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_ceiling');
    }
};
