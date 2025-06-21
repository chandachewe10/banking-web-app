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
       
           Schema::create('branches', function (Blueprint $table) {
       $table->id();

    // Branch Identity
    $table->string('name');                     // e.g. Lusaka Main, Kitwe West
    $table->string('code')->unique();           // Optional: e.g. BR001

    // Location Info
    $table->string('district');                 // e.g. Lusaka, Kasama
    $table->string('province');                 // Optional: for Zambian regional grouping
    $table->string('address')->nullable();      // Physical address
    $table->string('contact_number')->nullable();

    // Verification & Performance
    $table->boolean('active')->default(true);   // If branch is currently operating
    $table->timestamp('last_verified_at')->nullable(); // Last time a physical audit was done

    // Agent Supervision
    $table->unsignedInteger('agent_count')->default(0); // Calculated for performance

    $table->timestamps();
});
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
