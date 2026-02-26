<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('shop');
});

// ABA PayWay browser-redirect callbacks
Route::get('/payment/result', [PaymentController::class, 'result'])->name('payment.result');
Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
