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
            $table->timestamp('email_verified_at')->nullable();
            $table->string('action_at')->nullable();

            $table->string('company_name');

            // Home Address
            $table->string('home_street');
            $table->string('home_subdivision');
            $table->string('home_barangay');
            $table->string('home_city', 100);
            // Office Address
            $table->string('office_street');
            $table->string('office_subdivision');
            $table->string('office_barangay');
            $table->string('office_city', 100); 
            $table->string('mobile_no', 15);
            $table->string('telephone_no', 15);
            $table->date('birthdate')->nullable();
            $table->string('valid_id_no')->nullable();
            $table->string('id_type')->nullable();
            // $table->string('email')->unique();
            $table->string('civil_status');
            $table->string('citizenship');
            $table->string('payment_method');

            // Business Details
            $table->string('salesman_relationship')->nullable();
            $table->string('weekly_volume')->nullable();
            $table->string('other_products_interest')->nullable();
            $table->date('date_required')->nullable();
            $table->string('referred_by')->nullable();
            $table->string('product_requirements')->nullable();

            // Agreement
            $table->boolean('agreement')->default(false);

            $table->timestamps();
        });


        // authorized_representatives table
        Schema::create('representatives', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_id');
            $table->string('rep_last_name', 50);
            $table->string('rep_first_name', 50);
            $table->string('rep_middle_name', 50)->nullable();
            $table->string('rep_relationship', 50);
            $table->string('rep_contact_no', 15);
            $table->timestamps();
        });

        // authorized_signatories table
        Schema::create('signatories', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_id');
            $table->string('signatory_last_name', 50);
            $table->string('signatory_first_name', 50);
            $table->string('signatory_middle_name', 50)->nullable();
            $table->string('signatory_relationship', 50);
            $table->string('signatory_contact_no', 15);
            $table->timestamps();
        });

        // banks table
        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_id');
            $table->string('account_name')->nullable();
            $table->string('bank')->nullable();
            $table->string('branch')->nullable();
            $table->string('account_number')->nullable();
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
