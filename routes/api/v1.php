<?php

use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\PlanController;
use App\Http\Controllers\API\QuestionController;
use App\Http\Controllers\API\User\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\SubjectController;
use App\Http\Controllers\API\TopicController;
use App\Http\Controllers\API\UserSubjectController;

Route::controller(UserController::class)->group(function () {
    Route::get('user', 'user')->name('user');
    Route::get('users', 'users')->name('users');
});


Route::apiResources(['subjects' => SubjectController::class]);
Route::get('subjects/status/{subject}', [SubjectController::class, 'toggleStatus'])->name('subjects.toggleStatus');

Route::get('user-subjects', [UserSubjectController::class, 'userSubjects'])->name('user-subjects');
Route::post('user-subjects', [UserSubjectController::class, 'store'])->name('user-subjects.store');


Route::apiResource('courses', CourseController::class);
Route::get('courses/status/{course}', [CourseController::class, 'toggleStatus'])->name('courses.toggleStatus');

Route::apiResource('topics', TopicController::class);

Route::prefix('plans')->group(function () {
    Route::get('/', [PlanController::class, 'index']);
    Route::post('/store', [PlanController::class, 'store']);
    Route::get('/{id}', [PlanController::class, 'show']);
});

Route::get('topics/status/{topic}', [TopicController::class, 'toggleStatus'])->name('topics.toggleStatus');

Route::apiResource('questions', QuestionController::class);
Route::get('questions/status/{question}', [QuestionController::class, 'toggoleStatus'])->name('questions.toggleStatus');

