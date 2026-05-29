<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Borrower;
use App\Models\CreditEvaluation;
use App\Models\HeadCreditEvaluation;
use App\Models\BranchManagerEvaluation;
use App\Models\Loans;
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

            // The loan moves through a chain of separate tables, one per approval stage.
            // Each stage copies the decision flags forward, so the furthest record is the
            // most up to date. Existence of a downstream record means the prior stage was decided.
            $creditEval = CreditEvaluation::where('borrower_id', $borrower->id)->latest()->first();
            $headEval   = HeadCreditEvaluation::where('borrower_id', $borrower->id)->latest()->first();
            $branchEval = BranchManagerEvaluation::where('borrower_id', $borrower->id)->latest()->first();
            $loanRow    = Loans::where('borrower_id', $borrower->id)->latest()->first();

            // Build document checklist from Spatie Media
            $uploadedDocs = [];
            foreach (['id_front', 'id_back', 'selfie', 'bank_statement', 'payslip1', 'payslip2', 'payslip3'] as $col) {
                $uploadedDocs[$col] = $borrower->getMedia($col)->isNotEmpty();
            }
            $documentsComplete = ! in_array(false, array_values($uploadedDocs), true);

            $pipeline = $this->buildPipeline($creditEval, $headEval, $branchEval, $loanRow);
            $overall  = $this->overallStatus($creditEval, $loanRow, $pipeline);

            // Financials come from the original application (credit_evaluations), which is the
            // only table carrying disbursement_method; loan_number/status come from the chain.
            $finalStatus = $loanRow?->loan_status ?? $creditEval?->loan_status;

            return response()->json([
                'success' => true,
                'data'    => [
                    'step'               => $this->currentStep($borrower, $creditEval, $documentsComplete),
                    'overall_status'     => $overall['key'],
                    'status_label'       => $overall['label'],
                    'status_message'     => $overall['message'],
                    'borrower_id'        => $borrower->id,
                    'case_number'        => $creditEval?->case_number ?? $borrower->case_number,
                    'loan_status'        => $finalStatus,
                    'documents_complete' => $documentsComplete,
                    'uploaded_documents' => $uploadedDocs,
                    'pipeline'           => $pipeline,
                    'borrower'           => $borrower->only([
                        'first_name', 'last_name', 'email', 'mobile',
                        'identification', 'province', 'country',
                    ]),
                    'loan'               => $creditEval ? [
                        'principal_amount'    => $creditEval->principal_amount,
                        'loan_purpose'        => $creditEval->loan_purpose,
                        'interest_rate'       => $creditEval->interest_rate,
                        'interest_amount'     => $creditEval->interest_amount,
                        'monthly_repayment'   => $creditEval->monthly_repayment,
                        'disbursed_amount'    => $creditEval->disbursed_amount,
                        'total_repayment'     => $creditEval->total_repayment,
                        'duration_period'     => $creditEval->duration_period,
                        'loan_duration'       => $creditEval->loan_duration,
                        'disbursement_method' => $creditEval->disbursement_method,
                        'case_number'         => $creditEval->case_number,
                        'loan_status'         => $finalStatus,
                        'loan_number'         => $loanRow?->loan_number,
                    ] : null,
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

    /**
     * Build the loan approval pipeline from the chain of stage tables.
     *
     * A stage is "decided" only when its downstream record exists (the web app creates the
     * next stage's record when a reviewer saves a decision). The decision flags are copied
     * forward, so the furthest record holds the authoritative values. This avoids treating the
     * default `false` (0) on undecided steps as a rejection.
     */
    private function buildPipeline($creditEval, $headEval, $branchEval, $loanRow): array
    {
        // Furthest record carries the most complete set of decision flags.
        $flags       = $loanRow ?? $branchEval ?? $headEval ?? $creditEval;
        $finalStatus = $loanRow?->loan_status ?? $flags?->loan_status;

        $approved = fn ($v) => $v !== null && (int) $v === 1;
        $denied   = fn ($s) => in_array($s, ['denied', 'rejected'], true);

        $pipeline = [];

        // 1 — Application submitted
        $pipeline[] = [
            'key'   => 'submitted',
            'title' => 'Application Submitted',
            'state' => $creditEval ? 'completed' : 'pending',
        ];

        // 2 — Credit Officer (decided once a Head Credit record exists)
        $pipeline[] = [
            'key'   => 'credit_officer',
            'title' => 'Credit Officer Review',
            'state' => $this->stageState((bool) $creditEval, (bool) $headEval, $approved($flags?->is_approved_on_step_one)),
        ];

        // 3 — Head of Credit (decided once a Branch Manager record exists)
        $pipeline[] = [
            'key'   => 'head_credit',
            'title' => 'Head of Credit Review',
            'state' => $this->stageState((bool) $headEval, (bool) $branchEval, $approved($flags?->is_approved_on_step_two)),
        ];

        // 4 — Branch Manager (decided once a Loan record exists)
        $pipeline[] = [
            'key'   => 'branch_manager',
            'title' => 'Branch Manager Review',
            'state' => $this->stageState((bool) $branchEval, (bool) $loanRow, $approved($flags?->is_approved_on_step_three)),
        ];

        // 5 — Final approval & disbursement
        $finalState = 'pending';
        if ($loanRow) {
            if ($finalStatus === 'approved' || $approved($flags?->is_approved_on_step_four)) {
                $finalState = 'completed';
            } elseif ($denied($finalStatus)) {
                $finalState = 'rejected';
            } else {
                $finalState = 'current';
            }
        }
        $pipeline[] = [
            'key'   => 'disbursement',
            'title' => 'Final Approval & Disbursement',
            'state' => $finalState,
        ];

        return $pipeline;
    }

    /**
     * @param bool $reached      Has this stage's own record been created (it's in the queue)?
     * @param bool $decided      Has the next stage's record been created (a decision was saved)?
     * @param bool $approvedFlag Was the decision an approval?
     */
    private function stageState(bool $reached, bool $decided, bool $approvedFlag): string
    {
        if ($decided) return $approvedFlag ? 'completed' : 'rejected';
        if ($reached) return 'current';
        return 'pending';
    }

    private function overallStatus($creditEval, $loanRow, array $pipeline): array
    {
        if (! $creditEval) {
            return [
                'key'     => 'incomplete',
                'label'   => 'Application In Progress',
                'message' => 'Complete the remaining steps to submit your loan application.',
            ];
        }

        $finalStatus = $loanRow?->loan_status ?? $creditEval->loan_status;
        $states      = array_column($pipeline, 'state');

        if ($finalStatus === 'approved') {
            return [
                'key'     => 'approved',
                'label'   => 'Approved',
                'message' => 'Congratulations! Your loan has been approved and is being processed for disbursement.',
            ];
        }

        if (in_array('rejected', $states, true) || in_array($finalStatus, ['denied', 'rejected'], true)) {
            return [
                'key'     => 'rejected',
                'label'   => 'Not Approved',
                'message' => 'Unfortunately your application was not approved. Please contact your branch for details.',
            ];
        }

        return [
            'key'     => 'in_review',
            'label'   => 'Pending Approval',
            'message' => 'Your application has been submitted and is currently being reviewed by our credit team. We will notify you as it progresses.',
        ];
    }
}
