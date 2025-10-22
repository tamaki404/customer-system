<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string('review_id')->unique();
            $table->string('head', 100)->required(); 
            $table->string('body', 255)->required(); 
            $table->string('user_id', 30)->required(); 
            $table->enum('status', ['Active', 'Resolved', 'Under review', 'Cancelled'])->default('Active');
            $table->string('raised_by', 50)->required();
            $table->timestamp('raised_at')->required();
            $table->timestamp('resolved_at')->nullable();
            $table->string('resolved_by', 50)->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};


