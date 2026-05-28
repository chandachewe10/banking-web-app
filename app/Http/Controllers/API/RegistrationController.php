<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Notifications\OtpNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\User;

class RegistrationController extends Controller
{
    /**
     * Register a new mobile user (email + phone → send OTP).
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|max:255|unique:users,email',
                'phone' => 'required|numeric|unique:users,phone',
            ]);

            $user = User::create([
                'email' => $request->email,
                'phone' => $request->phone,
                'name'  => $request->email, // placeholder until profile is complete
            ]);

            $token = $user->createToken('mobile-client-token')->plainTextToken;

            // Send OTP
            $otpCode              = rand(100000, 999999);
            $user->otp_code       = $otpCode;
            $user->otp_expires_at = now()->addMinutes(10);
            $user->save();

            try {
                $user->notify(new OtpNotification($otpCode));
            } catch (\Exception $e) {
                Log::error('Registration: OTP email failed — ' . $e->getMessage());
            }

            $this->sendOtpSms($request->phone, $otpCode);

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully. OTP sent.',
                'data'    => [
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'token' => $token,
                ],
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return 422 with field-level errors so the mobile app can humanize them
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error'   => collect($e->errors())->flatten()->first(),
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('RegistrationController@store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function index() {}
    public function show(string $id) {}
    public function edit(string $id) {}
    public function update(Request $request, string $id) {}
    public function destroy(string $id) {}

    // ── OTP ──────────────────────────────────────────────────────

    public function verifyOtp(Request $request)
    {
        try {
            $request->validate([
                'otp_code' => 'required|numeric',
                'email'    => 'required|email|exists:users,email',
            ]);

            $user = User::where('email', $request->email)->first();

            if (
                ! $user ||
                (string) $user->otp_code !== (string) $request->otp_code ||
                now()->greaterThan($user->otp_expires_at)
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired OTP code.',
                    'error'   => 'Invalid or expired OTP code',
                ], 422);
            }

            $user->is_verified    = true;
            $user->otp_code       = null;
            $user->otp_expires_at = null;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully.',
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error'   => collect($e->errors())->flatten()->first(),
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('RegistrationController@verifyOtp: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function resendOtp(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email',
            ]);

            $user = User::where('email', $request->email)->first();

            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.',
                ], 404);
            }

            $otpCode              = rand(100000, 999999);
            $user->otp_code       = $otpCode;
            $user->otp_expires_at = now()->addMinutes(10);
            $user->save();

            try {
                $user->notify(new OtpNotification($otpCode));
            } catch (\Exception $e) {
                Log::error('resendOtp: OTP email failed — ' . $e->getMessage());
            }

            $this->sendOtpSms($user->phone, $otpCode);

            return response()->json([
                'success' => true,
                'message' => 'OTP resent successfully.',
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error'   => collect($e->errors())->flatten()->first(),
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('RegistrationController@resendOtp: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // ── SMS Helper ───────────────────────────────────────────────

    public function sendOtpSms($phoneNumber, $otp)
    {
        $message  = 'Your OTP verification code is ' . $otp . '. Valid for 10 minutes.';
        $base_uri = config('services.swiftsms.baseUri');
        $endpoint = config('services.swiftsms.endpoint');
        $senderId = config('services.swiftsms.senderId');
        $token    = config('services.swiftsms.token');

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ])->get($base_uri . $endpoint, [
                'sender_id' => $senderId,
                'numbers'   => $phoneNumber,
                'message'   => $message,
            ]);

            if (! $response->successful()) {
                Log::error('SwiftSMS send failed', ['response' => $response->body()]);
            }
        } catch (\Exception $e) {
            Log::error('SwiftSMS exception: ' . $e->getMessage());
        }
    }
}
