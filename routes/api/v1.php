<?php

use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\QuestionController;
use App\Http\Controllers\API\QuestionTypeController;
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
Route::get('courses/status/{course}', [CourseController::class, 'toggleStatus'])->name('courses.toggleStatus');

Route::apiResource('topics', TopicController::class);

Route::apiResource('question-types', QuestionTypeController::class);
Route::get('question-types/status/{question_type}', [QuestionTypeController::class, 'toggleStatus'])->name('question-types.toggleStatus');

Route::apiResource('questions', QuestionController::class);
Route::get('questions/status/{question}', [QuestionController::class, 'toggoleStatus'])->name('questions.toggleStatus');
