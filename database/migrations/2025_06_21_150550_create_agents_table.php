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
       Schema::create('agents', function (Blueprint $table) {
    $table->id();

    // Agent Identity
    $table->string('name');
    $table->string('national_id')->unique();      // NRC or ID number
    $table->string('phone')->unique();
    $table->string('email')->nullable();

    // Branch Relationship
    $table->foreignId('branch_id')->constrained()->onDelete('cascade');

    // Performance / Work
    $table->integer('cases_handled')->default(0);      // Total loans handled
    $table->integer('documents_collected')->default(0); // Optional tracking
    $table->decimal('performance_score', 5, 2)->nullable(); // Optional: based on task completion

    // Status & Tracking
    $table->boolean('active')->default(true);
    $table->timestamp('last_login_at')->nullable();     // If using auth

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
