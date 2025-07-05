<?php

use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\PlanController;
use App\Http\Controllers\API\QuestionAnswerController;
use App\Http\Controllers\API\QuestionController;
use App\Http\Controllers\API\QuestionDetailsController;
use App\Http\Controllers\ApI\QuizAnswerController;
use App\Http\Controllers\API\QuizController;
use App\Http\Controllers\API\QuizOptionController;
use App\Http\Controllers\API\User\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\SubjectController;
use App\Http\Controllers\API\TopicController;
use App\Http\Controllers\API\UserSubjectController;
use App\Http\Controllers\API\UserClassController;
use App\Http\Controllers\API\UserItemProgressController;
use App\Http\Controllers\API\UserProgressController;

Route::controller(UserController::class)->group(function () {
    Route::get('user', 'user')->name('user');
    Route::get('users', 'users')->name('users');
    Route::put('users', 'updateUser')->name('users.update');
});

Route::apiResource('user-classes', UserClassController::class);
Route::get('user-classes/status/{user_class}', [UserClassController::class, 'toggleStatus'])->name('user-classes.toggleStatus');

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

Route::apiResource('question-details', QuestionDetailsController::class);
Route::get('question-details/status/{question_detail}', [QuestionDetailsController::class, 'toggleStatus'])->name('question-details.toggleStatus');

Route::apiResource('question-answers', QuestionAnswerController::class);

Route::apiResource('quizzes', QuizController::class);
Route::get('quizzes/status/{quiz}', [QuizController::class, 'toggleStatus'])->name('quizzes.toggleStatus');

Route::apiResource('quiz-options', QuizOptionController::class);

Route::apiResource('quiz-answers', QuizAnswerController::class);

// Route::get('user-progress', [UserProgressController::class, 'userProgress'])->name('user-progress');
Route::post('user-progress', [UserProgressController::class, 'storeOrUpdateUserProgress'])->name('user-progress.store');

Route::get('/user-progress-list', [UserProgressController::class, 'index']);

Route::apiResource('user-item-progress', UserItemProgressController::class);
