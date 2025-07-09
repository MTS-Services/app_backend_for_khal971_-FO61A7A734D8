<?php

use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\PlanController;
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
    Route::get('users-access/{id}', 'userAccessByAdmin')->name('user-access');
    Route::put('users-update/{id}', 'userUpdateByAdmin')->name('user.update');
    Route::get('users', 'users')->name('users');
    Route::put('user-update', 'updateUser')->name('users.update');
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

Route::prefix('plans')->group(function () {
    Route::get('/', [PlanController::class, 'index']);
    Route::post('/store', [PlanController::class, 'store']);
    Route::get('/{id}', [PlanController::class, 'show']);
});

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
Route::get('quiz/answers/{quiz_id}', [QuizAnswerController::class, 'quizAnswers'])->name('quiz.answers', function ($id) {
    
});

// Route::get('user-progress', [UserProgressController::class, 'userProgress'])->name('user-progress');
// Route::post('user-progress', [UserProgressController::class, 'storeOrUpdateUserProgress'])->name('user-progress.store');

// Route::get('/user-progress-list', [UserProgressController::class, 'index']);

// Route::apiResource('user-item-progress', UserItemProgressController::class);
// Route::get('user-item-progress/toggle-bookmark/{bookmark}', [UserItemProgressController::class, 'toggleBookmark'])->name('toggle-bookmark');
// Route::get('user-item-progress/toggle-flag/{flag}', [UserItemProgressController::class, 'toggleFlag'])->name('toggle-flag');

// Route::apiResource('progress-milestones', ProgressMilestoneController::class);

// Route::apiResource('user-milestone-achievements', UserMilestoneAchievementController::class);
// Route::get('/progress/question/{userId}/{questionId}', [ProgressController::class, 'getQuestionProgress']);

// Existing routes (assuming these are already converted or exist)
// Route::get('/progress/question/{userId}/{questionId}', [ProgressControllerTest::class, 'getQuestionProgress']);
// Route::get('/progress/topic/{userId}/{topicId}', [ProgressControllerTest::class, 'getTopicProgress']);

// // New
// Route::get('/progress/topic/{userId}/{topicId}/questions', [ProgressControllerTest::class, 'getTopicQuestionsProgress']);
// Route::post('/progress/item/update', [ProgressControllerTest::class, 'updateItemProgress']);
// Route::get('/progress/next/{userId}', [ProgressControllerTest::class, 'getNextItemToStudy']);
// Route::post('/progress/batch', [ProgressControllerTest::class, 'getBatchProgress']);
// Route::post('/progress/bookmark', [ProgressControllerTest::class, 'toggleBookmark']);
// Route::post('/progress/flag', [ProgressControllerTest::class, 'toggleFlag']);
