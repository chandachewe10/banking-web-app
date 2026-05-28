<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The upfront_fees migration (2026_03_01) added these columns to the `loans`
 * table, but LoanDetailsController writes to `credit_evaluations`.
 * This migration adds the missing columns to the correct table.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('credit_evaluations', function (Blueprint $table) {
            // Upfront deduction fees (may not exist yet)
            if (! Schema::hasColumn('credit_evaluations', 'credit_life_fee')) {
                $table->decimal('credit_life_fee', 15, 2)->default(0)->after('insurance_fee');
            }
            if (! Schema::hasColumn('credit_evaluations', 'insurance_levy')) {
                $table->decimal('insurance_levy', 15, 2)->default(0)->after('credit_life_fee');
            }
            if (! Schema::hasColumn('credit_evaluations', 'credit_reference_fee')) {
                $table->decimal('credit_reference_fee', 15, 2)->default(0)->after('insurance_levy');
            }
            if (! Schema::hasColumn('credit_evaluations', 'collateral_fee')) {
                $table->decimal('collateral_fee', 15, 2)->default(0)->after('credit_reference_fee');
            }
            if (! Schema::hasColumn('credit_evaluations', 'documentation_fee')) {
                $table->decimal('documentation_fee', 15, 2)->default(0)->after('collateral_fee');
            }

            // Monthly & totals
            if (! Schema::hasColumn('credit_evaluations', 'admin_fee_per_month')) {
                $table->decimal('admin_fee_per_month', 15, 2)->default(0)->after('documentation_fee');
            }
            if (! Schema::hasColumn('credit_evaluations', 'monthly_repayment')) {
                $table->decimal('monthly_repayment', 15, 2)->default(0)->after('admin_fee_per_month');
            }
            if (! Schema::hasColumn('credit_evaluations', 'disbursed_amount')) {
                $table->decimal('disbursed_amount', 15, 2)->default(0)->after('monthly_repayment');
            }
        });
    }

    public function down(): void
    {
        Schema::table('credit_evaluations', function (Blueprint $table) {
            $table->dropColumn([
                'credit_life_fee', 'insurance_levy', 'credit_reference_fee',
                'collateral_fee', 'documentation_fee', 'admin_fee_per_month',
                'monthly_repayment', 'disbursed_amount',
            ]);
        });
    }
};
