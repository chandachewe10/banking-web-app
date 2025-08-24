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
            $table->string('crb_scoring')->nullable();
            $table->string('employer_verification')->nullable();
            $table->string('due_diligence')->nullable();
            $table->string('comments')->nullable();
            $table->string('credit_appraisal_report')->nullable();
            $table->unsignedBigInteger('verified_by');
            $table->foreign('verified_by')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            $table->boolean('is_approved_on_step_one')->default(false);
            $table->boolean('is_approved_on_step_two')->default(false);
            $table->boolean('is_approved_on_step_three')->default(false);
            $table->boolean('is_approved_on_step_four')->default(false);



        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->string('crb_scoring')->nullable();
            $table->string('employer_verification')->nullable();
            $table->string('due_diligence')->nullable();
            $table->string('comments')->nullable();
            $table->string('credit_appraisal_report')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->foreign('verified_by')
                    ->nullable()
                    ->references('id')
                    
                    ->on('users')
                    ->onDelete('cascade');
            $table->boolean('is_approved_on_step_one')->default(false);
            $table->boolean('is_approved_on_step_two')->default(false);
            $table->boolean('is_approved_on_step_three')->default(false);
            $table->boolean('is_approved_on_step_four')->default(false);
        });
    }
};
