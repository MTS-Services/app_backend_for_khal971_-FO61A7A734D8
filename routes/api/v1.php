<?php

use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\QuestionController;
use App\Http\Controllers\API\QuestionOptionController;
use App\Http\Controllers\API\QuestionTypeController;
use App\Http\Controllers\API\User\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\SubjectController;
use App\Http\Controllers\API\TopicController;
use App\Http\Controllers\API\UserClassController;

Route::controller(UserController::class)->group(function () {
    Route::get('user', 'user')->name('user');
    Route::get('users', 'users')->name('users');
    Route::put('users', 'updateUser')->name('users.update');
});

Route::apiResource('user-classes', UserClassController::class);
Route::get('user-classes/status/{user_class}', [UserClassController::class, 'toggleStatus'])->name('user-classes.toggleStatus');

Route::apiResources(['subjects' => SubjectController::class]);
Route::get('subjects/status/{subject}', [SubjectController::class, 'toggleStatus'])->name('subjects.toggleStatus');


Route::apiResource('courses', CourseController::class);
Route::get('courses/status/{course}', [CourseController::class, 'toggleStatus'])->name('courses.toggleStatus');

Route::apiResource('topics', TopicController::class);
Route::get('topics/status/{topic}', [TopicController::class, 'toggleStatus'])->name('topics.toggleStatus');

Route::apiResource('question-types', QuestionTypeController::class);
Route::get('question-types/status/{question_type}', [QuestionTypeController::class, 'toggleStatus'])->name('question-types.toggleStatus');

Route::apiResource('questions', QuestionController::class);
Route::get('questions/status/{question}', [QuestionController::class, 'toggoleStatus'])->name('questions.toggleStatus');

Route::apiResource('question-options', QuestionOptionController::class);
Route::get('question-options/status/{question_option}', [QuestionOptionController::class, 'toggleStatus'])->name('question-options.toggleStatus');
