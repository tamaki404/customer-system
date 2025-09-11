<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Users: store avatar/company image as base64 and mime
        Schema::table('users', function (Blueprint $table) {
            $table->mediumBlob('image')->nullable()->change();
            if (!Schema::hasColumn('users', 'image_mime')) {
                $table->string('image_mime')->nullable()->after('image');
            }
        });

        // Tickets: store attachment image as base64 and mime
        Schema::table('tickets', function (Blueprint $table) {
            $table->longText('image')->nullable()->change();
            if (!Schema::hasColumn('tickets', 'image_mime')) {
                $table->string('image_mime')->nullable()->after('image');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('image')->nullable()->change();
            if (Schema::hasColumn('users', 'image_mime')) {
                $table->dropColumn('image_mime');
            }
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->string('image')->nullable()->change();
            if (Schema::hasColumn('tickets', 'image_mime')) {
                $table->dropColumn('image_mime');
            }
        });
    }
};


