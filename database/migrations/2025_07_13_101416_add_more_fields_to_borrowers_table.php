<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('borrowers', function (Blueprint $table) {
            $table->string('middle_name')->nullable(); // add column
            $table->string('title')->nullable(); // add this
            $table->string('country')->nullable(); // add this
            $table->string('marital_status')->nullable(); // add this
            $table->string('employer')->nullable(); // add this
            $table->string('employee_number')->nullable(); // add this
            $table->string('employer_number')->nullable(); // add this
            $table->string('employer_address')->nullable(); // add this
            $table->date('employee_start_date')->nullable(); // add this
            $table->string('employer_email')->nullable(); // add this
            $table->decimal('monthly_income', 15, 2)->nullable(); // add this
        });
    }

    public function down(): void
    {
        Schema::table('borrowers', function (Blueprint $table) {
            $table->dropColumn([
                'middle_name',
                'title',
                'country',
                'marital_status',
                'employer',
                'employee_number',
                'employer_number',
                'employer_address',
                'employee_start_date',
                'employer_email',
                'monthly_income',
            ]);
        });
    }
};
