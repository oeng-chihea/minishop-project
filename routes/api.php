<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

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

// ABA PayWay - initiate checkout (called by Vue frontend)
Route::post('/checkout', [PaymentController::class, 'initiate']);

// ABA PayWay - server-to-server webhook (called by ABA servers)
Route::post('/payment/webhook', [PaymentController::class, 'webhook']);
