<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Borrower;

class PersonalDetailsController extends Controller
{
    public function index() {}
    public function create() {}
    public function show(string $id) {}
    public function edit(string $id) {}
    public function update(Request $request, string $id) {}
    public function destroy(string $id) {}

    /**
     * Create or update a borrower record for the authenticated mobile user.
     *
     * The endpoint is idempotent: if a borrower row already exists for the
     * supplied email it is updated in-place rather than rejected. This lets
     * users correct mistakes and resume a partially-completed KYC application
     * without hitting "already registered" errors.
     */
    public function store(Request $request)
    {
        try {
            // ── 1. Look up any existing borrower BEFORE validation so we can
            //       exclude their own record from the unique checks.
            $existing   = Borrower::where('email', $request->email)->first();
            $excludeId  = $existing?->id ?? 'NULL';

            $validatedData = $request->validate([
                'firstName'         => 'required|string|max:255',
                'lastName'          => 'required|string|max:255',
                'middleName'        => 'nullable|string|max:255',
                'gender'            => 'required|in:male,female,other',
                // Exclude the current borrower's own row from uniqueness checks
                'citizenId'         => "required|string|max:50|unique:borrowers,identification,{$excludeId}",
                'title'             => 'required|string|max:10',
                'dateOfBirth'       => 'required',
                'phoneNumber'       => "required|string|max:20|unique:borrowers,mobile,{$excludeId}",
                'email'             => "required|email|max:255|unique:borrowers,email,{$excludeId}",
                'address'           => 'required|string|max:500',
                'latitude'          => 'nullable|numeric|between:-90,90',
                'longitude'         => 'nullable|numeric|between:-180,180',
                'district'          => 'required|string|max:100',
                'province'          => 'required|string|max:100',
                'country'           => 'required|string|max:100',
                'maritalStatus'     => 'required|string|max:20',
                'zipCode'           => 'nullable|string|max:10',
                'occupation'        => 'nullable|string|max:100',
                'employer'          => 'nullable|string|max:255',
                'employeeNumber'    => 'nullable|string|max:100',
                'employerNumber'    => 'nullable|string|max:100',
                'employerAddress'   => 'nullable|string|max:255',
                'employeeStartDate' => 'nullable|date',
                'employerEmail'     => 'nullable|email|max:255',
                'monthlyIncome'     => 'nullable|numeric|min:0',
                'bankName'          => 'nullable|string|max:255',
                'branchName'        => 'nullable|string|max:255',
                'branchCode'        => 'nullable|string|max:50',
                'accountNumber'     => 'nullable|string|max:50',
                'accountType'       => 'nullable|string|max:100',
            ]);

            // ── 2. Build the attribute map shared between create and update.
            $attributes = [
                'first_name'          => $validatedData['firstName'],
                'last_name'           => $validatedData['lastName'],
                'middle_name'         => $validatedData['middleName'] ?? null,
                'gender'              => $validatedData['gender'],
                'identification'      => $validatedData['citizenId'],
                'title'               => $validatedData['title'],
                'dob'                 => $validatedData['dateOfBirth'],
                'mobile'              => $validatedData['phoneNumber'],
                'address'             => $validatedData['address'],
                'latitude'            => $validatedData['latitude'] ?? null,
                'longitude'           => $validatedData['longitude'] ?? null,
                'city'                => $validatedData['district'],
                'province'            => $validatedData['province'],
                'country'             => $validatedData['country'],
                'marital_status'      => $validatedData['maritalStatus'],
                'zipcode'             => $validatedData['zipCode'] ?? null,
                'occupation'          => $validatedData['occupation'] ?? null,
                'employer'            => $validatedData['employer'] ?? null,
                'employee_number'     => $validatedData['employeeNumber'] ?? null,
                'employer_number'     => $validatedData['employerNumber'] ?? null,
                'employer_address'    => $validatedData['employerAddress'] ?? null,
                'employee_start_date' => $validatedData['employeeStartDate'] ?? null,
                'employer_email'      => $validatedData['employerEmail'] ?? null,
                'monthly_income'      => $validatedData['monthlyIncome'] ?? null,
                'bank_name'           => $validatedData['bankName'] ?? null,
                'bank_branch'         => $validatedData['branchName'] ?? null,
                'bank_sort_code'      => $validatedData['branchCode'] ?? null,
                'bank_account_number' => $validatedData['accountNumber'] ?? null,
                'bank_account_type'   => $validatedData['accountType'] ?? null,
            ];

            // ── 3. Upsert — create a new row or update the existing one.
            $borrower = Borrower::updateOrCreate(
                ['email' => $validatedData['email']],
                $attributes
            );

            $isNew = $borrower->wasRecentlyCreated;

            return response()->json([
                'success' => true,
                'message' => $isNew
                    ? 'Personal details saved successfully.'
                    : 'Personal details updated successfully.',
                'data'    => $borrower,
            ], $isNew ? 201 : 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error'   => collect($e->errors())->flatten()->first(),
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('PersonalDetailsController@store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
