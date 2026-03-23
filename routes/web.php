<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
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

// ── Admin routes (defined before catch-all so they are matched first) ──
Route::prefix('admin')->group(function () {
    Route::get('/',       [AdminController::class, 'index'])->name('admin.index');
    Route::get('/login',  [AdminController::class, 'loginForm'])->name('admin.login');
    Route::post('/login', [AdminController::class, 'login'])->name('admin.login.post');
    Route::post('/logout',[AdminController::class, 'logout'])->name('admin.logout');
    Route::post('/orders/{id}/status', [AdminController::class, 'updateStatus'])->name('admin.orders.status');
    Route::get('/data', [AdminController::class, 'data'])->name('admin.data');
});

// SPA fallback — serves the Vue app for any unknown path (must be last)
Route::get('/{any}', function () {
    return view('shop');
})->where('any', '^(?!admin).*$');
