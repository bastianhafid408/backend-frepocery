<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\MidtransController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\Transactionntroller;
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

// mengambil API dari app - usercontroller
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('user', [UserController::class, 'fetch']);
    Route::post('user', [UserController::class, 'updateProfile']);
    Route::post('user/photo', [UserController::class, 'updatePhoto']);
    Route::post('logout', [UserController::class, 'logout']);

    Route::get('transaction', [Transactionntroller::class, 'all']);
    Route::post('transaction/{id}', [Transactionntroller::class, 'update']);
    Route::post('checkout', [Transactionntroller::class, 'checkout']);
});

Route::post('login', [UserController::class, 'login']);
Route::post('register', [UserController::class, 'register']);
Route::get('product', [ProductController::class, 'all']);

Route::post('midtrans/callback', [MidtransController::class, 'callback']);
