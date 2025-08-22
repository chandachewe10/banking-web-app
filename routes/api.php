<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\{RegistrationController,PersonalDetailsController};
use App\Http\Controllers\API\DocumentController;
use App\Http\Controllers\API\LoanDetailsController;
use App\Http\Controllers\API\SignatureController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::resource('register', RegistrationController::class)->only(['index', 'store', 'update', 'destroy']);
Route::post('/verifyOtp', [RegistrationController::class, 'verifyOtp'])
->middleware('auth:sanctum');
Route::resource('personalDetails', PersonalDetailsController::class)->only(['index', 'store', 'update', 'destroy'])
->middleware('auth:sanctum');
Route::post('documents',[DocumentController::class,'uploadDocuments'])
->middleware('auth:sanctum');
Route::resource('loanDetails', LoanDetailsController::class)->only(['index', 'store', 'update', 'destroy'])
->middleware('auth:sanctum');
Route::resource('signature', SignatureController::class)->only(['index', 'store', 'update', 'destroy'])
->middleware('auth:sanctum');

