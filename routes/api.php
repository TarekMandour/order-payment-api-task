<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/check-phone', [AuthController::class, 'checkPhone']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
});

Route::middleware('jwt')->group(function () {

    Route::get('/auth/profile', [AuthController::class, 'profile']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::prefix('order')->group(function () {
        Route::get('/list', [OrderController::class, 'list']);
        Route::post('/checkout', [OrderController::class, 'checkout']);
        Route::post('/successful', [OrderController::class, 'successful']);
        Route::post('/failed', [OrderController::class, 'failed']);
        Route::delete('/destroy/{order}', [OrderController::class, 'destroy']);
    });

});