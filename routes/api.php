<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::resource('otp', ApiController::class)->only(['index', 'store', 'update', 'destroy']);
Route::post('/verifyOtp', [ApiController::class, 'verifyOtp'])
->middleware('auth:sanctum');

