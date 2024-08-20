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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// we need to check if user is already logged in by adding middlware
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


// Zakat -------------
Route::get('/{user}/zakat', [ZakatController::class, 'userZakats']);
Route::apiResource('zakat', ZakatController::class);

// Products -------------
Route::get('/{user}/product', [ProductController::class, 'userProducts']);
Route::apiResource('products', ProductController::class);
