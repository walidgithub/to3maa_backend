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

// Auth actions ------------------------------------------------------------------
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// we need to check if user is already logged in by adding middlware
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::put('/forgotPass', [AuthController::class, 'forgotPass']);
Route::put('/resetPass', [AuthController::class, 'resetPass']);

// Zakat --------------------------------------------------------------------------
Route::get('zakats', [ZakatController::class, 'userZakats']);
Route::delete('/deleteAllUserZakats', [ZakatController::class, 'deleteAllUserZakats']);
Route::get('/{zakat}/showZakatProducts', [ZakatController::class, 'showZakatProducts']);
Route::get('/getUserProductTotals', [ZakatController::class, 'getUserProductTotals']);
Route::apiResource('zakat', ZakatController::class);


// Products ------------------------------------------------------------------------
Route::apiResource('products', ProductController::class);
Route::get('/product', [ProductController::class, 'userProducts']);

/*
   Cpanel Username: b14_37248124
   Cpanel Password: To3maa_online
   Your URL: http://to3maa.byethost14.com or http://www.to3maa.byethost14.com
   FTP Server : ftpupload.net
   FTP Login : b14_37248124
   FTP Password : To3maa_online
   MySQL Database Name: MUST CREATE IN CPANEL
   MySQL Username : b14_37248124
   MySQL Password : To3maa_online
   MySQL Server: SEE THE CPANEL
   Cpanel URL: http://cpanel.byethost14.com/
*/
