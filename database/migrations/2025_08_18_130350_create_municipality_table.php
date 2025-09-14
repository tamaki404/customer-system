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
        Schema::create('municipality', function (Blueprint $table) {
            $table->id('municipality_id');
            $table->unsignedBigInteger('province_id');
            $table->string('municipality_name');
            $table->timestamps();

            $table->foreign('province_id')->references('province_id')->on('province')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('municipality');
    }
};
