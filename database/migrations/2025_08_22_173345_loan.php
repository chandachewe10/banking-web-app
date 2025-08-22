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
        Schema::table('loans', function (Blueprint $table) {
            $table->string('loan_purpose')->nullable();
            $table->decimal('interest_rate', 10, 2)->nullable();
            $table->decimal('interest_amount', 10, 2)->nullable();
            $table->decimal('processing_fee', 10, 2)->nullable();
            $table->decimal('arrangement_fee', 10, 2)->nullable();
            $table->decimal('insurance_fee', 10, 2)->nullable();
            $table->string('case_number')->nullable();
            $table->decimal('total_repayment', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            //
        });
    }
};
