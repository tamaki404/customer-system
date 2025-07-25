<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('receipts', function (Blueprint $table) {
        $table->dateTime('verified_at')->nullable()->change();
    });
}

public function down()
{
    Schema::table('receipts', function (Blueprint $table) {
        $table->date('verified_at')->nullable()->change();
    });
}
};
