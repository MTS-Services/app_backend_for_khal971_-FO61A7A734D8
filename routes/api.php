<?php

use App\Http\Controllers\API\AuthenticationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(AuthenticationController::class)->prefix('v1/auth')->name('api.v1.auth.')->group(function () {
    Route::post('register', 'register')->name('register');
    Route::post('login', 'login')->name('login');
    Route::post('verify-otp', 'verifyOtp')->name('verify-otp');
    Route::post('resent-otp', 'resendOtp')->name('resent-otp');

    Route::post('forgot-password', 'forgot')->name('forgot-password');
    Route::post('reset-password', 'resetPassword')->name('reset-password');

    Route::post('logout', 'logout')->name('logout')->middleware(['auth:api', 'verified-via-otp']);
    Route::post('logout/another', 'logoutAnother')->name('logoutAnother');
});
