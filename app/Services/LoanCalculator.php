<?php

namespace App\Services;

/**
 * Loan fee and repayment calculations aligned with the mobile app / admin form labels.
 */
class LoanCalculator
{
    public const ARRANGEMENT_RATE = 0.04;

    public const PROCESSING_RATE = 0.025;

    public const CREDIT_LIFE_RATE = 0.045;

    public const INSURANCE_LEVY = 150;

    public const CRB_FEE = 50;

    public const COLLATERAL_RATE = 0.01;

    public const DOCUMENTATION_RATE = 0.005;

    public const ADMIN_RATE_PER_MONTH = 0.005;

    public const DEFAULT_ANNUAL_INTEREST_RATE = 0.32;

    /**
     * @param  array{principal_amount?: mixed, loan_duration?: mixed, interest_rate?: mixed}  $input
     * @return array<string, float|int|string>
     */
    public static function calculate(array $input): array
    {
        $principal = max(0, (float) ($input['principal_amount'] ?? 0));
        $tenure    = max(1, (int) ($input['loan_duration'] ?? 1));
        $rate      = self::normalizeAnnualRate($input['interest_rate'] ?? self::DEFAULT_ANNUAL_INTEREST_RATE);

        $arrangementFee     = self::round($principal * self::ARRANGEMENT_RATE);
        $processingFee      = self::round($principal * self::PROCESSING_RATE);
        $creditLifeFee      = self::round($principal * self::CREDIT_LIFE_RATE);
        $insuranceLevy      = self::INSURANCE_LEVY;
        $creditReferenceFee = self::CRB_FEE;
        $collateralFee      = self::round($principal * self::COLLATERAL_RATE);
        $documentationFee   = self::round($principal * self::DOCUMENTATION_RATE);
        $adminFeePerMonth = self::round($principal * self::ADMIN_RATE_PER_MONTH);
        $totalAdminFees   = self::round($adminFeePerMonth * $tenure);

        // Kept in sync with LoanDetailsController insurance_fee column.
        $insuranceFee = $creditLifeFee;

        $totalInterest = self::round($principal * $rate * ($tenure / 12));
        $totalRepayment = self::round($principal + $totalInterest);
        $monthlyRepayment = self::round($totalRepayment / $tenure);

        // Per specification: only these 5 fees are physically deducted from the cash disbursed.
        // Collateral, documentation and admin fees are informational charges, not cash deductions.
        $upfrontDeductions = $arrangementFee
            + $processingFee
            + $creditLifeFee
            + $insuranceLevy
            + $creditReferenceFee;

        $disbursedAmount = self::round(max(0, $principal - $upfrontDeductions));

        return [
            'principal_amount'     => $principal,
            'loan_duration'        => $tenure,
            'duration_period'      => "{$tenure} months",
            'arrangement_fee'      => $arrangementFee,
            'processing_fee'       => $processingFee,
            'credit_life_fee'      => $creditLifeFee,
            'insurance_fee'        => $insuranceFee,
            'insurance_levy'       => $insuranceLevy,
            'credit_reference_fee' => $creditReferenceFee,
            'collateral_fee'       => $collateralFee,
            'documentation_fee'    => $documentationFee,
            'admin_fee_per_month'  => $adminFeePerMonth,
            'interest_amount'      => $totalInterest,
            'monthly_repayment'    => $monthlyRepayment,
            'total_repayment'      => $totalRepayment,
            'disbursed_amount'     => $disbursedAmount,
        ];
    }

    public static function normalizeAnnualRate(mixed $rate): float
    {
        $rate = (float) $rate;

        if ($rate <= 0) {
            return self::DEFAULT_ANNUAL_INTEREST_RATE;
        }

        // Accept 32 (percent) or 0.32 (decimal).
        return $rate > 1 ? $rate / 100 : $rate;
    }

    private static function round(float $value, int $precision = 2): float
    {
        return round($value, $precision);
    }
}
