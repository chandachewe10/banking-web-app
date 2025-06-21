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
        Schema::create('disbursements', function (Blueprint $table) {
    $table->id();
    $table->foreignId('loan_id')->constrained()->onDelete('cascade');  
    $table->decimal('amount', 12, 2);        
    $table->string('method');                 
    $table->string('reference_number')->nullable(); 
    $table->date('disbursed_at');           
    $table->boolean('authorized')->default(false); 
    $table->foreignId('authorized_by')->nullable()->constrained('users')->nullOnDelete();
    $table->text('notes')->nullable();       
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disbursements');
    }
};
