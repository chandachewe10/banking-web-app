<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Loans;
use App\Models\Borrower;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SignatureController extends Controller
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
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'signatureUri' => 'required|string',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Find user by email
            $borrowerFiles = Borrower::where('email', $request->email)->first();

            if (!$borrowerFiles) {
                return response()->json([
                    'success' => false,
                    'message' => 'Borrower not found'
                ], 404);
            }

            // Process each base64 file
            $uploadedFiles = [];

            if ($request->has('signatureUri')) {
                $uploadedFiles['signatureUri'] = $this->processBase64File($request->signatureUri, 'signatureUri', $borrowerFiles->id, ['jpeg', 'png', 'pdf']);
            }


            // Add media to user document
            foreach ($uploadedFiles as $collection => $fileInfo) {
                $borrowerFiles->addMedia($fileInfo['path'])
                    ->toMediaCollection($collection, 'borrowers');
            }

            $caseNumber = rand(100000, 999999);
           $caseHandler = User::role('Credit Officer') 
          ->whereNull('case_number')             
          ->latest()->first();
            $caseHandler->case_number = $caseNumber;
            $caseHandler->save();

            //Update the CaseNumber into the Loan Number
            $borrower = Borrower::where('email', "=", $request->email)->latest()->first();
            $loan = Loans::where('borrower_id', "=", $borrower->id)->where('loan_status', "=", 'processing')->first();
            $loan->case_number = $caseNumber;
            $loan->save();


            //Update the CaseNumber to the Borrower
            $borrower = Borrower::where('email', "=", $request->email)->latest()->first();
            $borrower->save();



            return response()->json([
                'success' => true,
                'message' => 'Signature uploaded successfully',
                'data' => [
                    'email' => $borrowerFiles->email,
                    'uploaded_files' => array_keys($uploadedFiles),
                    'caseNumber' => $caseNumber
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Signature upload failed: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Process base64 file and save to storage
     */
    private function processBase64File($base64Data, $prefix, $userId, $allowedExtensions = ['jpeg', 'png', 'pdf'])
    {
        // Extract mime type and base64 data
        if (!preg_match('/^data:([a-zA-Z]+\/[a-zA-Z\+]+);base64,/', $base64Data, $matches)) {
            throw new \Exception("Invalid base64 format for {$prefix}");
        }

        $mimeType = $matches[1];
        $extension = $this->mimeToExtension($mimeType);

        // Validate extension
        if (!in_array($extension, $allowedExtensions)) {
            throw new \Exception("Invalid file type for {$prefix}. Allowed: " . implode(', ', $allowedExtensions));
        }

        // Extract base64 data
        $data = substr($base64Data, strpos($base64Data, ',') + 1);
        $fileData = base64_decode($data, true);

        if ($fileData === false) {
            throw new \Exception("Invalid base64 data for {$prefix}");
        }

        // Validate file size (5MB = 5 * 1024 * 1024 bytes)
        $fileSize = strlen($fileData);
        if ($fileSize > 5120 * 1024) {
            throw new \Exception("File size too large for {$prefix}. Maximum 5MB allowed");
        }

        // Generate unique filename
        $filename = "{$prefix}_{$userId}_" . Str::random(10) . '.' . $extension;
        $path = "{$userId}/{$filename}";

        // Save file to storage
        Storage::disk('borrowers')->put($path, $fileData);

        return [
            'path' => Storage::disk('borrowers')->path($path),
            'filename' => $filename,
            'size' => $fileSize,
            'mime_type' => $mimeType
        ];
    }



    /**
     * Convert mime type to file extension
     */
    private function mimeToExtension($mimeType)
    {
        $mimeMap = [
            'image/jpeg' => 'jpeg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'application/pdf' => 'pdf',
            'image/webp' => 'webp',
        ];

        return $mimeMap[$mimeType] ?? 'bin';
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
