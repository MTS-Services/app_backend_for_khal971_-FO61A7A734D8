<?php

use App\Http\Controllers\API\BookmarkController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\PlanController;
use App\Http\Controllers\API\PracticeController;
use App\Http\Controllers\Api\ProgressController;
use App\Http\Controllers\Api\ProgressControllerTest;
use App\Http\Controllers\API\ProgressMilestoneController;
use App\Http\Controllers\API\QuestionAnswerController;
use App\Http\Controllers\API\QuestionController;
use App\Http\Controllers\API\QuestionDetailsController;
use App\Http\Controllers\API\QuizAnswerController;
use App\Http\Controllers\API\QuizController;
use App\Http\Controllers\API\QuizOptionController;
use App\Http\Controllers\API\User\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\SubjectController;
use App\Http\Controllers\API\TopicController;
use App\Http\Controllers\API\UserSubjectController;
use App\Http\Controllers\API\UserClassController;
use App\Http\Controllers\API\UserItemProgressController;
use App\Http\Controllers\API\UserMilestoneAchievementController;
use App\Http\Controllers\API\UserProgressController;

Route::controller(UserController::class)->group(function () {
    Route::get('user', 'user')->name('user');
    Route::get('user-access/admin/{id}', 'userAccessByAdmin')->name('user.access.admin');
    Route::put('user-update/admin/{id}', 'userUpdateByAdmin')->name('user.update.admin');
    Route::get('users', 'users')->name('users');
    Route::put('user-update', 'updateUser')->name('user.update');
    Route::delete('user-delete/{id}', 'destroy')->name('user.destroy');
});



Route::apiResource('user-classes', UserClassController::class);
Route::get('user-classes/status/{user_class}', [UserClassController::class, 'toggleStatus'])->name('user-classes.toggleStatus');

Route::apiResources(['subjects' => SubjectController::class]);
Route::get('subjects/status/{subject}', [SubjectController::class, 'toggleStatus'])->name('subjects.toggleStatus');

Route::get('user-subjects', [UserSubjectController::class, 'userSubjects'])->name('user-subjects');
Route::post('user-subjects', [UserSubjectController::class, 'store'])->name('user-subjects.store');


Route::apiResource('courses', CourseController::class);
Route::get('subject-courses/{subject_id}', [CourseController::class, 'subjectCourses'])->name('subject-courses');
Route::get('courses/status/{course}', [CourseController::class, 'toggleStatus'])->name('courses.toggleStatus');

Route::apiResource('topics', TopicController::class);
Route::get('course-topics/{course_id}', [TopicController::class, 'courseTopics'])->name('course-topics');
Route::get('topics/status/{topic}', [TopicController::class, 'toggleStatus'])->name('topics.toggleStatus');

Route::apiResource('plans', PlanController::class);
Route::get('plans/status/{plan}', [PlanController::class, 'toggleStatus'])->name('plans.toggleStatus');

Route::apiResource('question-details', QuestionDetailsController::class);
Route::get('topic-question-details/{topic_id}', [QuestionDetailsController::class, 'topicQuestionDetails'])->name('topic-question-details');
Route::get('question-details/status/{question_detail}', [QuestionDetailsController::class, 'toggleStatus'])->name('question-details.toggleStatus');

Route::apiResource('questions', QuestionController::class);
Route::get('question-details/questions/{question_details_id}', [QuestionController::class, 'questions'])->name('question-details.questions');
Route::get('questions/status/{question}', [QuestionController::class, 'toggoleStatus'])->name('questions.toggleStatus');


Route::apiResource('question-answers', QuestionAnswerController::class);
Route::get('questons/answers/{question_id}', [QuestionAnswerController::class, 'questionAnswers'])->name('questions.answers');

Route::apiResource('quizzes', QuizController::class);
Route::get('topic-quizzes/{topic_id}', [QuizController::class, 'quizzes'])->name('topic-quizzes');
Route::get('quizzes/status/{quiz}', [QuizController::class, 'toggleStatus'])->name('quizzes.toggleStatus');

Route::apiResource('quiz-options', QuizOptionController::class);
Route::get('quiz/options/{quiz_id}', [QuizOptionController::class, 'options'])->name('quiz.options');

Route::apiResource('quiz-answers', QuizAnswerController::class);

// Practice and Bookmark 
Route::get('/bookmarked/questions', [BookmarkController::class, 'bookmarkedQuestions'])->name('bookmark-questions');
Route::get('/bookmarked/quizzes', [BookmarkController::class, 'bookmarkedQuizzes'])->name('bookmark-quizzes');

Route::get('/practices/quizzes', [PracticeController::class, 'quizzes'])->name('practices.quizzes');
Route::get('/practices/questions', [PracticeController::class, 'questions'])->name('practices.questions');
Route::get('/practices/topics', [PracticeController::class, 'topics'])->name('practices.topics');
Route::get('/practices/courses', [PracticeController::class, 'courses'])->name('practices.courses');
