<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_verification_tokens', function (Blueprint $table) {
            $table->id();
            // Store our custom users.user_id string identifier for easier linking
            $table->string('user_id');
            $table->string('email');
            $table->string('token')->unique();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_verification_tokens');
    }
};


