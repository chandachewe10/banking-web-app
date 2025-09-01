<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Borrower;

class PersonalDetailsController extends Controller
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
        'firstName' => 'required|string|max:255',
        'lastName' => 'required|string|max:255',
        'middleName' => 'nullable|string|max:255',
        'gender' => 'required|in:male,female',
        'citizenId' => 'required|string|max:50|unique:borrowers,identification',
        'title' => 'required|string|max:10',
        'dateOfBirth' => 'required',
        'phoneNumber' => 'required|string|max:20|unique:borrowers,mobile',
        'email' => 'required|email|max:255|unique:borrowers,email',
        'address' => 'required|string|max:500',
        'district' => 'required|string|max:100',
        'province' => 'required|string|max:100',
        'country' => 'required|string|max:100',
        'maritalStatus' => 'required|string|max:20',
        'zipCode' => 'nullable|string|max:10',
        'occupation' => 'nullable|string|max:100',
        'employer' => 'nullable|string|max:255',
        'employeeNumber' => 'nullable|string|max:100',
        'employerNumber' => 'nullable|string|max:100',
        'employerAddress' => 'nullable|string|max:255',
        'employeeStartDate' => 'nullable|date',
        'employerEmail' => 'nullable|email|max:255',
        'monthlyIncome' => 'nullable|numeric|min:0',
        'bankName' => 'nullable|string|max:255',
        'branchName' => 'nullable|string|max:255',
        'branchCode' => 'nullable|string|max:50',
        'accountNumber' => 'nullable|string|max:50',
        'accountType' => 'nullable|string|max:250',
    ]);

    $borrower = Borrower::create([
    'first_name' => $validatedData['firstName'],
    'last_name' => $validatedData['lastName'],
    'middle_name' => $validatedData['middleName'] ?? null,
    'gender' => $validatedData['gender'],
    'identification' => $validatedData['citizenId'],
    'title' => $validatedData['title'],
    'dob' => $validatedData['dateOfBirth'],
    'mobile' => $validatedData['phoneNumber'],
    'email' => $validatedData['email'],
    'address' => $validatedData['address'],
    'city' => $validatedData['district'],
    'province' => $validatedData['province'],
    'country' => $validatedData['country'],
    'marital_status' => $validatedData['maritalStatus'],
    'zipcode' => $validatedData['zipCode'] ?? null,
    'occupation' => $validatedData['occupation'] ?? null,
    'employer' => $validatedData['employer'] ?? null,
    'employee_number' => $validatedData['employeeNumber'] ?? null,
    'employer_number' => $validatedData['employerNumber'] ?? null,
    'employer_address' => $validatedData['employerAddress'] ?? null,
    'employee_start_date' => $validatedData['employeeStartDate'] ?? null,
    'employer_email' => $validatedData['employerEmail'] ?? null,
    'monthly_income' => $validatedData['monthlyIncome'] ?? null,
    'bank_name' => $validatedData['bankName'] ?? null,
    'bank_branch' => $validatedData['branchName'] ?? null,
    'bank_sort_code' => $validatedData['branchCode'] ?? null,
    'bank_account_number' => $validatedData['accountNumber'] ?? null,
    'bank_account_name' => $validatedData['accountType'] ?? null,
]);


    return response()->json([
        'success' => true,
        'message' => 'Borrower registered successfully',
        'data' => $borrower
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
