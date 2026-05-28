<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Borrower;
use App\Models\CreditEvaluation;
use App\Models\User;
use App\Notifications\OtpNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Mobile login: find user by phone, issue token, send OTP.
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'phone' => 'required|string',
            ]);

            $user = User::where('phone', $request->phone)->first();

            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No account found with this phone number. Please register first.',
                ], 404);
            }

            // Rotate tokens — keep only one active mobile session
            $user->tokens()->where('name', 'mobile-login-token')->delete();
            $token = $user->createToken('mobile-login-token')->plainTextToken;

            // Generate fresh OTP
            $otpCode = rand(100000, 999999);
            $user->otp_code       = $otpCode;
            $user->otp_expires_at = now()->addMinutes(10);
            $user->save();

            // Email notification
            try {
                $user->notify(new OtpNotification($otpCode));
            } catch (\Exception $e) {
                Log::error('AuthController: OTP email failed — ' . $e->getMessage());
            }

            // SMS notification (reuse helper from RegistrationController)
            try {
                (new RegistrationController())->sendOtpSms($user->phone, $otpCode);
            } catch (\Exception $e) {
                Log::error('AuthController: OTP SMS failed — ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'OTP sent to your registered email and phone.',
                'data'    => [
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'token' => $token,
                ],
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Phone number is required.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('AuthController@login: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Return the KYC application status for the authenticated mobile user.
     * Used by the mobile app to determine where to resume after login.
     */
    public function applicationStatus(Request $request)
    {
        try {
            $user     = $request->user();
            $borrower = Borrower::where('email', $user->email)->first();

            if (! $borrower) {
                return response()->json([
                    'success' => true,
                    'data'    => [
                        'step'      => 'biodata',
                        'completed' => false,
                        'message'   => 'Personal details not yet submitted.',
                    ],
                ]);
            }

            $loan = CreditEvaluation::where('borrower_id', $borrower->id)
                ->latest()
                ->first();

            // Build document checklist from Spatie Media
            $mediaCollections = $borrower->getMediaCollections();
            $uploadedDocs     = [];
            foreach (['id_front', 'id_back', 'selfie', 'bank_statement', 'payslip1', 'payslip2', 'payslip3'] as $col) {
                $uploadedDocs[$col] = $borrower->getMedia($col)->isNotEmpty();
            }
            $documentsComplete = ! in_array(false, array_values($uploadedDocs), true);

            return response()->json([
                'success' => true,
                'data'    => [
                    'step'               => $this->currentStep($borrower, $loan, $documentsComplete),
                    'borrower_id'        => $borrower->id,
                    'case_number'        => $borrower->case_number,
                    'loan_status'        => $loan?->loan_status,
                    'documents_complete' => $documentsComplete,
                    'uploaded_documents' => $uploadedDocs,
                    'borrower'           => $borrower->only([
                        'first_name', 'last_name', 'email', 'mobile',
                        'identification', 'province', 'country',
                    ]),
                    'loan'               => $loan ? $loan->only([
                        'id', 'loan_status', 'principal_amount', 'loan_purpose',
                        'monthly_repayment', 'total_repayment', 'case_number',
                        'is_approved_on_step_one', 'is_approved_on_step_two',
                        'is_approved_on_step_three', 'is_approved_on_step_four',
                    ]) : null,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('AuthController@applicationStatus: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Could not retrieve application status.',
            ], 500);
        }
    }

    private function currentStep($borrower, $loan, bool $documentsComplete): string
    {
        if (! $borrower)         return 'biodata';
        if (! $documentsComplete) return 'documents';
        if (! $loan)             return 'loan_details';
        return 'completed';
    }
}
