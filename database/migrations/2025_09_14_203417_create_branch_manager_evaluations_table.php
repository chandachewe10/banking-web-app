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
    Schema::create('branch_manager_evaluations', function (Blueprint $table) {
        $table->id();

        $table->unsignedBigInteger('borrower_id');
        $table->unsignedBigInteger('loan_type_id');
        $table->string('loan_status')->nullable();
        $table->date('loan_release_date')->nullable();
        $table->decimal('principal_amount', 15, 2)->nullable();
        $table->text('loan_purpose')->nullable();
        $table->decimal('interest_rate', 5, 2)->nullable();
        $table->decimal('interest_amount', 15, 2)->nullable();
        $table->decimal('processing_fee', 15, 2)->nullable();
        $table->decimal('arrangement_fee', 15, 2)->nullable();
        $table->decimal('insurance_fee', 15, 2)->nullable();
        $table->decimal('total_repayment', 15, 2)->nullable();
        $table->string('case_number')->nullable();
        $table->integer('loan_duration')->nullable();
        $table->string('duration_period')->nullable();
        $table->string('email')->nullable();
        $table->string('crb_scoring')->nullable();
        $table->boolean('employer_verification')->default(false);
        $table->boolean('due_diligence')->default(false);
        $table->text('comments')->nullable();
        $table->text('credit_appraisal_report')->nullable();
        $table->unsignedBigInteger('verified_by')->nullable();

        $table->boolean('is_approved_on_step_one')->default(false);
        $table->boolean('is_approved_on_step_two')->default(false);
        $table->boolean('is_approved_on_step_three')->default(false);
        $table->boolean('is_approved_on_step_four')->default(false);

        $table->string('loan_number')->nullable();
        $table->boolean('physical_verification')->default(false);
        $table->string('loan_agreement_file_path')->nullable();

        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_manager_evaluations');
    }
};
