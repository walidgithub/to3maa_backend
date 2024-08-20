<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;

// Auth -----------------
// get user data
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// we need to check if user is already logged in by adding middlware
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


// Zakat -------------

// Products -------------
Route::apiResource('products', ProductController::class);
