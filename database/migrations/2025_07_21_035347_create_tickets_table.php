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
        Schema::create('tickets', function (Blueprint $table) {

            $table -> string ('title');
            $table -> longText ('body');
            $table->string('image')->nullable();
            $table->date('startDate');
            $table->integer('id');
            $table->date('endDate');
            $table->string('status')->default('open');
            $table->integer('received_by')->nullable();
            $table -> date('resolved_at')->default(today());

            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
