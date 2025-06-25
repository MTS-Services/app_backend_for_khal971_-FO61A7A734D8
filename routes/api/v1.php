<?php

use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\User\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\SubjectController;
use App\Http\Controllers\API\TopicController;

Route::controller(UserController::class)->group(function () {
    Route::get('user', 'user')->name('user');
    Route::get('users', 'users')->name('users');
});


Route::apiResources(['subjects' => SubjectController::class]);
Route::get('subjects/status/{subject}', [SubjectController::class, 'toggleStatus'])->name('subjects.toggleStatus');


Route::apiResource('courses', CourseController::class);
Route::apiResource('topics', TopicController::class);
