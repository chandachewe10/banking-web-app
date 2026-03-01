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
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'amount'              => 'required|numeric',
                'purpose'             => 'required|string|max:255',
                'interestRate'        => 'nullable|numeric',
                'tenure'              => 'required|numeric',

                // Upfront deduction fees
                'arrangementFee'      => 'required|numeric',
                'processingFee'       => 'required|numeric',
                'creditLifeFee'       => 'required|numeric',
                'insuranceLevy'       => 'required|numeric',
                'creditReferenceFee'  => 'required|numeric',
                'collateralFee'       => 'required|numeric',
                'documentationFee'    => 'required|numeric',

                // Monthly & totals
                'adminFeePerMonth'    => 'required|numeric',
                'totalInterestFee'    => 'required|numeric',
                'monthlyRepayment'    => 'required|numeric',
                'disbursedAmount'     => 'required|numeric',
                'totalRepayable'      => 'required|numeric',

                'email'               => 'required|string|email',
            ]);

            $borrower = Borrower::where('email', $validatedData['email'])->firstOrFail();

            $loan = CreditEvaluation::create([
                'borrower_id'         => $borrower->id,
                'loan_type_id'        => 1,
                'loan_status'         => 'processing',
                'loan_release_date'   => Carbon::now(),

                // Loan basics
                'principal_amount'    => $validatedData['amount'],
                'loan_purpose'        => $validatedData['purpose'],
                'interest_rate'       => $validatedData['interestRate'],
                'loan_duration'       => $validatedData['tenure'],
                'duration_period'     => $validatedData['tenure'],

                // Upfront deduction fees
                'arrangement_fee'     => $validatedData['arrangementFee'],
                'processing_fee'      => $validatedData['processingFee'],
                'credit_life_fee'     => $validatedData['creditLifeFee'],
                'insurance_levy'      => $validatedData['insuranceLevy'],
                'credit_reference_fee'=> $validatedData['creditReferenceFee'],
                'collateral_fee'      => $validatedData['collateralFee'],
                'documentation_fee'   => $validatedData['documentationFee'],

                // Monthly & totals
                'admin_fee_per_month' => $validatedData['adminFeePerMonth'],
                'interest_amount'     => $validatedData['totalInterestFee'],
                'monthly_repayment'   => $validatedData['monthlyRepayment'],
                'disbursed_amount'    => $validatedData['disbursedAmount'],
                'total_repayment'     => $validatedData['totalRepayable'],

                'verified_by'         => null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Loan details submitted successfully',
                'data'    => $loan
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Borrower not found with the provided email',
            ], 404);

        } catch (\Exception $e) {
            Log::error('LoanDetailsController@store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}