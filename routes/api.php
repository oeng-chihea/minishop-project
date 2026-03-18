<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BakongController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Bakong KHQR - generate QR code for cart (called by Vue frontend)
Route::post('/bakong/checkout', [BakongController::class, 'initiate']);

// Bakong KHQR - poll transaction status by MD5 (called by Vue frontend every few seconds)
Route::post('/bakong/check-status', [BakongController::class, 'checkStatus']);

// Temporary diagnostic route - remove after debugging
Route::get('/bakong/diagnose', [BakongController::class, 'diagnose']);
