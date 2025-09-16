<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_id');
            $table->string('type'); 
            $table->string('file_name');
            $table->string('file_mime');
            $table->unsignedBigInteger('file_size');
            $table->timestamps();
        });

        // Add MEDIUMBLOB column separately
        DB::statement('ALTER TABLE documents ADD file MEDIUMBLOB NULL');
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
