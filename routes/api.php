<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ZakatController;
use App\Http\Controllers\ProductController;

// Auth -----------------
// get user data
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Email Verification -----------------------------------------------------------
// email verificaion handler
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->middleware(['auth:sanctum', 'signed']);

// User actions ------------------------------------------------------------------
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// we need to check if user is already logged in by adding middlware
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


// Zakat --------------------------------------------------------------------------
Route::get('zakats', [ZakatController::class, 'userZakats']);
Route::delete('/deleteAllUserZakats', [ZakatController::class, 'deleteAllUserZakats']);
Route::get('/{zakat}/showZakatProducts', [ZakatController::class, 'showZakatProducts']);
Route::get('/getUserProductTotals', [ZakatController::class, 'getUserProductTotals']);
Route::apiResource('zakat', ZakatController::class);


// Products ------------------------------------------------------------------------
Route::apiResource('products', ProductController::class);
Route::get('/product', [ProductController::class, 'userProducts']);
