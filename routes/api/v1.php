<?php

use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\User\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\SubjectController;


Route::controller(UserController::class)->group(function () {
    Route::get('user', 'user')->name('user');
    Route::get('users', 'users')->name('users');
    Route::post('logout', 'logout')->name('logout');
});


Route::apiResources(['subject' => SubjectController::class]);
Route::apiResource('course', CourseController::class);
