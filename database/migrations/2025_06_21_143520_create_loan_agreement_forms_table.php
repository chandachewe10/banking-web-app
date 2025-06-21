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
        Schema::create('loan_agreement_forms', function (Blueprint $table) {
            $table->id();                 
                $table->unsignedBigInteger('loan_type_id');
                $table->text('loan_agreement_text');
                $table->timestamps();
                $table->foreign('loan_type_id')
                    ->references('id')
                    ->on('loan_types')
                    ->onDelete('restrict'); 
                     $table->integer('activate_loan_agreement_form');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_agreement_forms');
    }
};
