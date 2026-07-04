<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CompanyController;
use App\Http\Controllers\API\JobListingController;
use App\Http\Controllers\API\ApplicationController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::get('/companies', [CompanyController::class, 'index']);
Route::get('/companies/{company}', [CompanyController::class, 'show']);
Route::get('/job-listings', [JobListingController::class, 'index']);
Route::get('/job-listings/{jobListing}', [JobListingController::class, 'show']);


Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/change-password', [AuthController::class, 'changePassword']);

    Route::post('/companies', [CompanyController::class, 'store']);
    Route::put('/companies/{company}', [CompanyController::class, 'update']);
    Route::delete('/companies/{company}', [CompanyController::class, 'destroy']);

    Route::post('/job-listings', [JobListingController::class, 'store']);
    Route::put('/job-listings/{jobListing}', [JobListingController::class, 'update']);
    Route::delete('/job-listings/{jobListing}', [JobListingController::class, 'destroy']);

    Route::apiResource('applications', ApplicationController::class);
});

Route::fallback(function () {
    return response()->json([
        'message' => 'Stranica nije pronadjena'
    ], 404);
});