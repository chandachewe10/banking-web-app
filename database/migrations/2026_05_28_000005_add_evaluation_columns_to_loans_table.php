<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            if (! Schema::hasColumn('loans', 'loan_purpose')) {
                $table->text('loan_purpose')->nullable()->after('principal_amount');
            }
            if (! Schema::hasColumn('loans', 'interest_rate')) {
                $table->decimal('interest_rate', 5, 2)->nullable()->after('loan_purpose');
            }
            if (! Schema::hasColumn('loans', 'interest_amount')) {
                $table->decimal('interest_amount', 15, 2)->nullable()->after('interest_rate');
            }
            if (! Schema::hasColumn('loans', 'processing_fee')) {
                $table->decimal('processing_fee', 15, 2)->nullable()->after('interest_amount');
            }
            if (! Schema::hasColumn('loans', 'arrangement_fee')) {
                $table->decimal('arrangement_fee', 15, 2)->nullable()->after('processing_fee');
            }
            if (! Schema::hasColumn('loans', 'insurance_fee')) {
                $table->decimal('insurance_fee', 15, 2)->nullable()->after('arrangement_fee');
            }
            if (! Schema::hasColumn('loans', 'total_repayment')) {
                $table->decimal('total_repayment', 15, 2)->nullable()->after('insurance_fee');
            }
            if (! Schema::hasColumn('loans', 'case_number')) {
                $table->string('case_number')->nullable()->after('total_repayment');
            }
            if (! Schema::hasColumn('loans', 'loan_number')) {
                $table->string('loan_number')->nullable()->unique()->after('case_number');
            }
            if (! Schema::hasColumn('loans', 'email')) {
                $table->string('email')->nullable()->after('loan_number');
            }
            if (! Schema::hasColumn('loans', 'crb_scoring')) {
                $table->string('crb_scoring')->nullable()->after('email');
            }
            if (! Schema::hasColumn('loans', 'employer_verification')) {
                $table->string('employer_verification', 50)->nullable()->after('crb_scoring');
            }
            if (! Schema::hasColumn('loans', 'due_diligence')) {
                $table->text('due_diligence')->nullable()->after('employer_verification');
            }
            if (! Schema::hasColumn('loans', 'comments')) {
                $table->text('comments')->nullable()->after('due_diligence');
            }
            if (! Schema::hasColumn('loans', 'credit_appraisal_report')) {
                $table->text('credit_appraisal_report')->nullable()->after('comments');
            }
            if (! Schema::hasColumn('loans', 'verified_by')) {
                $table->unsignedBigInteger('verified_by')->nullable()->after('credit_appraisal_report');
            }
            if (! Schema::hasColumn('loans', 'is_approved_on_step_one')) {
                $table->boolean('is_approved_on_step_one')->default(false)->after('verified_by');
            }
            if (! Schema::hasColumn('loans', 'is_approved_on_step_two')) {
                $table->boolean('is_approved_on_step_two')->default(false)->after('is_approved_on_step_one');
            }
            if (! Schema::hasColumn('loans', 'is_approved_on_step_three')) {
                $table->boolean('is_approved_on_step_three')->default(false)->after('is_approved_on_step_two');
            }
            if (! Schema::hasColumn('loans', 'is_approved_on_step_four')) {
                $table->boolean('is_approved_on_step_four')->default(false)->after('is_approved_on_step_three');
            }
            if (! Schema::hasColumn('loans', 'physical_verification')) {
                $table->boolean('physical_verification')->default(false)->after('is_approved_on_step_four');
            }
            if (! Schema::hasColumn('loans', 'loan_agreement_file_path')) {
                $table->string('loan_agreement_file_path')->nullable()->after('physical_verification');
            }
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn([
                'loan_purpose',
                'interest_rate',
                'interest_amount',
                'processing_fee',
                'arrangement_fee',
                'insurance_fee',
                'total_repayment',
                'case_number',
                'loan_number',
                'email',
                'crb_scoring',
                'employer_verification',
                'due_diligence',
                'comments',
                'credit_appraisal_report',
                'verified_by',
                'is_approved_on_step_one',
                'is_approved_on_step_two',
                'is_approved_on_step_three',
                'is_approved_on_step_four',
                'physical_verification',
                'loan_agreement_file_path',
            ]);
        });
    }
};
