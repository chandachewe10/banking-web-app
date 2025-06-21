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
        Schema::create('borrowers', function (Blueprint $table) {
             $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('gender')->nullable();
            $table->string('dob')->nullable();
            $table->string('occupation')->nullable();
            $table->string('identification')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('zipcode')->nullable();
            $table->string('next_of_kin_first_name')->nullable();
            $table->string('next_of_kin_last_name')->nullable();
            $table->string('phone_next_of_kin')->nullable();
            $table->string('address_next_of_kin')->nullable();
            $table->string('relationship_next_of_kin')->nullable();
             $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('bank_sort_code')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('mobile_money_name')->nullable();
            $table->string('mobile_money_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrowers');
    }
};
