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
        Schema::create('suppliers', function (Blueprint $table) {
            //account verification /ids
            $table->id();
            $table->string('user_id');
            $table->string('supplier_id');
            $table->timestamp('staff_id')->nullable();
            $table->string('category');
            $table->string('citizenship');
            $table->string('payment_method');
            $table->string('mobile', 11);
            $table->string('tele', 11)->nullable();
            $table->string('id_mime_type')->nullable();
            $table->string('id_filename')->nullable();
            $table->unsignedInteger('id_size')->nullable(); 
            $table->string('id_type');
            $table->string('id_number', 100);
            $table->date('birthdate')->nullable();

            $table->timestamps();
        });
        DB::statement('ALTER TABLE suppliers ADD id_image MEDIUMBLOB NULL');


        // authorized_representatives table
        Schema::create('representatives', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('rep_lastname', 50);
            $table->string('rep_firstname', 50);
            $table->string('rep_middlename', 50)->nullable();
            $table->string('auth_position', 50);
            $table->string('rep_contact', 15);
            $table->timestamps();
        });

        // authorized_signatories table
        Schema::create('signatories', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('sign_lastname', 50);
            $table->string('sign_firstname', 50);
            $table->string('sign_middlename', 50)->nullable();
            $table->string('sign_position', 50);

            $table->string('e_mime_type')->nullable();
            $table->string('e_filename')->nullable();
            $table->unsignedInteger('image_size')->nullable(); 
            $table->timestamps();
        });
        DB::statement('ALTER TABLE signatories ADD e_image MEDIUMBLOB NULL');

        // banks table
        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('account_name')->nullable();
            $table->string('bank')->nullable();
            $table->string('branch')->nullable();
            $table->string('account_number')->nullable();
            $table->timestamps();
        });

        // business table
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('years')->nullable();
            $table->string('referred_by')->nullable();
            $table->string('contacted_by')->nullable();
            $table->timestamps();
        });



    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
