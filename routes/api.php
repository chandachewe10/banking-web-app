<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\{RegistrationController,PersonalDetailsController};

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::resource('register', RegistrationController::class)->only(['index', 'store', 'update', 'destroy']);
Route::post('/verifyOtp', [RegistrationController::class, 'verifyOtp'])
->middleware('auth:sanctum');
Route::resource('personalDetails', PersonalDetailsController::class)->only(['index', 'store', 'update', 'destroy'])
->middleware('auth:sanctum');

