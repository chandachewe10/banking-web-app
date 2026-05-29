<?php

use App\Models\LoanType;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $defaults = [
            'Agri Loan',
            'Personal Loan',
            'Business Loan',
            'Salary Loan',
        ];

        foreach ($defaults as $name) {
            LoanType::firstOrCreate(['loan_name' => $name]);
        }
    }

    public function down(): void
    {
        LoanType::whereIn('loan_name', [
            'Agri Loan',
            'Personal Loan',
            'Business Loan',
            'Salary Loan',
        ])->delete();
    }
};
