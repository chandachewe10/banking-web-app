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

            
$table->decimal('credit_life_fee', 15, 2)->default(0); 
           $table->decimal('insurance_levy', 15, 2)->default(0);   
                    $table->decimal('credit_reference_fee', 15, 2)->default(0);            $table->decimal('collateral_fee', 15, 2)->default(0);            $table->decimal('documentation_fee', 15, 2)->default(0);

            $table->decimal('admin_fee_per_month', 15, 2)->default(0);            $table->decimal('monthly_repayment', 15, 2)->default(0);            $table->decimal('disbursed_amount', 15, 2)->default(0);
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
