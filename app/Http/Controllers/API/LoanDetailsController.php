<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Borrower;
use App\Models\Loans;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LoanDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{


    $validatedData = $request->validate([
        'amount' => 'required|numeric',
        'purpose' => 'required|string|max:255',
        'interestRate' => 'nullable|numeric',
        'tenure' => 'required|numeric',
        'arrangementFee' => 'required|numeric',
        'processingFee' => 'required|numeric',
        'insuranceFee' => 'required|numeric',
        'totalInterestFee' => 'required|numeric',
        'email' => 'required|string',

    ]);



    $borrower = Borrower::where('email', $request->email)->first();
    $loan = Loans::create([
        'borrower_id' => $borrower->id,
        'loan_type_id' => 1,
        'loan_status' => 'processing',
        'loan_release_date' => Carbon::now(),
        'principal_amount' => $validatedData['amount'],
        'loan_purpose' => $validatedData['purpose'],
        'interest_rate' => $validatedData['interestRate'],
        'loan_duration' => $validatedData['tenure'],
        'duration_period' => $validatedData['tenure'],
        'interest_amount' => $validatedData['totalInterestFee'],
        'arrangement_fee' => $validatedData['arrangementFee'],
        'processing_fee' => $validatedData['processingFee'],
        'insurance_fee' => $validatedData['insuranceFee'],
        'total_repayment' => ($validatedData['amount'] + $validatedData['totalInterestFee']),
        'verified_by' => NULL


    ]);

    return response()->json([
        'success' => true,
        'message' => 'Loan details submitted successfully',
        'data' => $loan
    ], 201);
}

    catch(\Exception $e){
                Log::error('Error in store method: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request',
                'error' => $e->getMessage(),
            ], 500);
        }



    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
