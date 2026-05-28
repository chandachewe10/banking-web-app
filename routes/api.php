<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\RegistrationController;
use App\Http\Controllers\API\PersonalDetailsController;
use App\Http\Controllers\API\DocumentController;
use App\Http\Controllers\API\LoanDetailsController;
use App\Http\Controllers\API\SignatureController;

// ── Authenticated user (web panel) ──────────────────────────────────
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// ── Mobile: Registration & OTP ──────────────────────────────────────
Route::resource('register', RegistrationController::class)
    ->only(['index', 'store', 'update', 'destroy']);

Route::post('/verifyOtp', [RegistrationController::class, 'verifyOtp'])
    ->middleware('auth:sanctum');

Route::post('/resendOtp', [RegistrationController::class, 'resendOtp'])
    ->middleware('auth:sanctum');


// ── Mobile: Login (returning users) ─────────────────────────────────
Route::post('/login', [AuthController::class, 'login']);

Route::get('/application-status', [AuthController::class, 'applicationStatus'])
    ->middleware('auth:sanctum');


// ── Mobile: KYC steps (all require a valid Sanctum token) ───────────
Route::middleware('auth:sanctum')->group(function () {
    Route::resource('personalDetails', PersonalDetailsController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::post('documents', [DocumentController::class, 'uploadDocuments']);

    Route::resource('loanDetails', LoanDetailsController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::resource('signature', SignatureController::class)
        ->only(['index', 'store', 'update', 'destroy']);
});
