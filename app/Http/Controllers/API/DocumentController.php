<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Borrower;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    public function uploadDocuments(Request $request)
    {
        
        Log::info('FRONT ID: '.$request->idFront);
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'idFront' => 'required|string',
            'idBack' => 'required|string',
            'selfie' => 'required|string',
            'bankStatement' => 'required|string',
            'payslip' => 'required|string',
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
            $user = Borrower::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Process each base64 file
            $uploadedFiles = [];

            if ($request->has('idFront')) {
                $uploadedFiles['id_front'] = $this->processBase64File($request->idFront, 'id_front', $user->id, ['jpeg', 'png', 'pdf']);
            }

            if ($request->has('idBack')) {
                $uploadedFiles['id_back'] = $this->processBase64File($request->idBack, 'id_back', $user->id, ['jpeg', 'png', 'pdf']);
            }

            if ($request->has('selfie')) {
                $uploadedFiles['selfie'] = $this->processBase64File($request->selfie, 'selfie', $user->id, ['jpeg', 'png']);
            }

            if ($request->has('bankStatement')) {
                $uploadedFiles['bank_statement'] = $this->processBase64File($request->bankStatement, 'bank_statement', $user->id, ['jpeg', 'png', 'pdf']);
            }

            if ($request->has('payslip')) {
                $uploadedFiles['payslip'] = $this->processBase64File($request->payslip, 'payslip', $user->id, ['jpeg', 'png', 'pdf']);
            }

            // Add media to user document
            foreach ($uploadedFiles as $collection => $fileInfo) {
                $user->addMedia($fileInfo['path'])
                    ->toMediaCollection($collection,'borrowers');
            }

            return response()->json([
                'success' => true,
                'message' => 'Files uploaded successfully',
                'data' => [
                    'email' => $user->email,
                    'user_id' => $user->id,
                    'uploaded_files' => array_keys($uploadedFiles)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'File upload failed: ' . $e->getMessage()
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


}
