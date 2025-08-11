<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            // Store image data directly as base64 (can be large)
            $table->longText('receipt_image')->nullable()->change();
            // Store the image MIME type to build data URI when rendering
            if (!Schema::hasColumn('receipts', 'receipt_image_mime')) {
                $table->string('receipt_image_mime')->nullable()->after('receipt_image');
            }
        });
    }

    public function down(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            // Revert to filename storage
            $table->string('receipt_image')->nullable()->change();
            if (Schema::hasColumn('receipts', 'receipt_image_mime')) {
                $table->dropColumn('receipt_image_mime');
            }
        });
    }
};


