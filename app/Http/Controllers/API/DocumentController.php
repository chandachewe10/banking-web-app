<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Borrower;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    public function uploadDocuments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'         => 'required|email',
            'idFront'       => 'required|file|mimes:jpg,jpeg,png,webp|max:10240',
            'idBack'        => 'required|file|mimes:jpg,jpeg,png,webp|max:10240',
            'selfie'        => 'required|file|mimes:jpg,jpeg,png,webp|max:10240',
            'bankStatement' => 'required|file|mimes:pdf|max:10240',
            'payslip1'      => 'required|file|mimes:pdf|max:10240',
            'payslip2'      => 'required|file|mimes:pdf|max:10240',
            'payslip3'      => 'required|file|mimes:pdf|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $user = Borrower::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            }

            $collections = [
                'idFront'       => 'id_front',
                'idBack'        => 'id_back',
                'selfie'        => 'selfie',
                'bankStatement' => 'bank_statement',
                'payslip1'      => 'payslip1',
                'payslip2'      => 'payslip2',
                'payslip3'      => 'payslip3',
            ];

            $uploaded = [];
            foreach ($collections as $field => $collection) {
                if ($request->hasFile($field)) {
                    $user->addMedia($request->file($field))
                         ->toMediaCollection($collection, 'borrowers');
                    $uploaded[] = $collection;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Files uploaded successfully',
                'data'    => [
                    'email'          => $user->email,
                    'user_id'        => $user->id,
                    'uploaded_files' => $uploaded,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Document upload failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'File upload failed: ' . $e->getMessage(),
            ], 500);
        }
    }


}
