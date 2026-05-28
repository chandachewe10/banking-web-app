<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Borrower;
use App\Models\CreditEvaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LoanDetailsController extends Controller
{
    public function index() {}
    public function show(string $id) {}
    public function update(Request $request, string $id) {}
    public function destroy(string $id) {}

    /**
     * Store loan details submitted from the mobile KYC form.
     *
     * Required fields (always sent by mobile):
     *   amount, purpose, tenure, arrangementFee, processingFee,
     *   insuranceFee, totalInterestFee, email
     *
     * Optional extended fields (defaulted / calculated if absent):
     *   interestRate, creditLifeFee, insuranceLevy, creditReferenceFee,
     *   collateralFee, documentationFee, adminFeePerMonth,
     *   monthlyRepayment, disbursedAmount, totalRepayable
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'amount'             => 'required|numeric|min:1',
                'purpose'            => 'required|string|max:255',
                'interestRate'       => 'nullable|numeric',
                'tenure'             => 'required|numeric|min:1',
                'arrangementFee'     => 'required|numeric|min:0',
                'processingFee'      => 'required|numeric|min:0',
                'insuranceFee'       => 'required|numeric|min:0',
                'totalInterestFee'   => 'required|numeric|min:0',
                'email'              => 'required|email',
                // Optional — mobile may compute and send these for precision
                'creditLifeFee'      => 'nullable|numeric|min:0',
                'insuranceLevy'      => 'nullable|numeric|min:0',
                'creditReferenceFee' => 'nullable|numeric|min:0',
                'collateralFee'      => 'nullable|numeric|min:0',
                'documentationFee'   => 'nullable|numeric|min:0',
                'adminFeePerMonth'   => 'nullable|numeric|min:0',
                'monthlyRepayment'   => 'nullable|numeric|min:0',
                'disbursedAmount'    => 'nullable|numeric|min:0',
                'totalRepayable'     => 'nullable|numeric|min:0',
            ]);

            $borrower = Borrower::where('email', $validatedData['email'])->firstOrFail();

            // Core numbers
            $amount         = (float) $validatedData['amount'];
            $tenure         = (int)   $validatedData['tenure'];
            $interestRate   = (float) ($validatedData['interestRate'] ?? 0.32);
            $arrangementFee = (float) $validatedData['arrangementFee'];
            $processingFee  = (float) $validatedData['processingFee'];
            $insuranceFee   = (float) $validatedData['insuranceFee'];
            $totalInterest  = (float) $validatedData['totalInterestFee'];

            // Derive totals when not provided
            $allUpfrontFees   = $arrangementFee + $processingFee + $insuranceFee;
            $disbursedAmount  = isset($validatedData['disbursedAmount'])
                ? (float) $validatedData['disbursedAmount']
                : round($amount - $allUpfrontFees, 2);
            $totalRepayable   = isset($validatedData['totalRepayable'])
                ? (float) $validatedData['totalRepayable']
                : round($amount + $totalInterest, 2);
            $monthlyRepayment = isset($validatedData['monthlyRepayment'])
                ? (float) $validatedData['monthlyRepayment']
                : ($tenure > 0 ? round($totalRepayable / $tenure, 2) : 0);
            $adminFeePerMonth = (float) ($validatedData['adminFeePerMonth']   ?? 0);

            // Supplementary fee breakdown (default 0 unless mobile sends them)
            $creditLifeFee      = (float) ($validatedData['creditLifeFee']      ?? 0);
            $insuranceLevy      = (float) ($validatedData['insuranceLevy']      ?? 0);
            $creditReferenceFee = (float) ($validatedData['creditReferenceFee'] ?? 0);
            $collateralFee      = (float) ($validatedData['collateralFee']      ?? 0);
            $documentationFee   = (float) ($validatedData['documentationFee']   ?? 0);

            $loan = CreditEvaluation::create([
                'borrower_id'          => $borrower->id,
                'loan_type_id'         => 1,
                'loan_status'          => 'processing',
                'loan_release_date'    => Carbon::now(),
                'email'                => $validatedData['email'],

                // Loan basics
                'principal_amount'     => $amount,
                'loan_purpose'         => $validatedData['purpose'],
                'interest_rate'        => $interestRate,
                'loan_duration'        => $tenure,
                'duration_period'      => $tenure . ' months',

                // Upfront fees
                'arrangement_fee'      => $arrangementFee,
                'processing_fee'       => $processingFee,
                'insurance_fee'        => $insuranceFee,
                'credit_life_fee'      => $creditLifeFee,
                'insurance_levy'       => $insuranceLevy,
                'credit_reference_fee' => $creditReferenceFee,
                'collateral_fee'       => $collateralFee,
                'documentation_fee'    => $documentationFee,

                // Monthly & totals
                'admin_fee_per_month'  => $adminFeePerMonth,
                'interest_amount'      => $totalInterest,
                'monthly_repayment'    => $monthlyRepayment,
                'disbursed_amount'     => $disbursedAmount,
                'total_repayment'      => $totalRepayable,

                'verified_by'          => null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Loan details submitted successfully.',
                'data'    => $loan,
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'error'   => collect($e->errors())->flatten()->first(),
                'errors'  => $e->errors(),
            ], 422);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'No borrower found with the provided email. Please complete personal details first.',
            ], 404);

        } catch (\Exception $e) {
            Log::error('LoanDetailsController@store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
