<?php

namespace App\Http\Controllers\API\Temporary;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Practice;
use App\Models\Question;
use App\Models\QuestionDetails;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ChangeItToOriginalMethod extends Controller
{
    public function questionSubmit(Request $request, int $id)
    {
        try {
            $isAnswerCorrect = 0;

            DB::transaction(function () use ($id, $request, &$isAnswerCorrect) {
                $question = Question::find($id);
                if (!$question) {
                    throw new \Exception('Question not found');
                }

                $isAnswerCorrect = (bool) random_int(0, 1);
                $question->load('questionDetails.topic.course.subject');


                /* ===================================== =====================================
                     Start Question Details progress insert or update in practice table
                ===================================== ===================================== */

                $questionDetails = $question->questionDetails()
                    ->withCount('questions')
                    ->first();

                // Get total questions from relationship
                $totalQuestions = $questionDetails->questions_count ?? 0;

                // Get existing practice data (if exists)
                $existingQuestionDetailPractice = Practice::where([
                    'practiceable_id' => $questionDetails->id,
                    'practiceable_type' => QuestionDetails::class,
                    'user_id' => Auth::user()->id,
                ])->first();

                $totalAttempts = ($existingQuestionDetailPractice->total_attempts ?? 0) + 1;
                $correctAttempts = ($existingQuestionDetailPractice->correct_attempts ?? 0) + ($isAnswerCorrect ? 1 : 0);
                $wrongAttempts = ($existingQuestionDetailPractice->wrong_attempts ?? 0) + (!$isAnswerCorrect ? 1 : 0);

                $progress = $totalQuestions > 0
                    ? min(100, round(($correctAttempts / $totalQuestions) * 100, 2))
                    : 0;

                $progressStatus = match (true) {
                    $totalAttempts === 0 => Practice::STATUS_NOT_STARTED,
                    $progress >= 100 => Practice::STATUS_COMPLETED,
                    default => Practice::STATUS_IN_PROGRESS,
                };

                // Insert or update the practice
                Practice::updateOrCreate(
                    [
                        'practiceable_id' => $questionDetails->id,
                        'practiceable_type' => QuestionDetails::class,
                        'user_id' => Auth::user()->id,
                    ],
                    [
                        'total_attempts' => $totalAttempts,
                        'correct_attempts' => $correctAttempts,
                        'wrong_attempts' => $wrongAttempts,
                        'progress' => $progress,
                        'status' => $progressStatus,
                    ]
                );

                /* ===================================== =====================================
                     Start Topic progress insert or update in practice table
                ===================================== ===================================== */

                $existingTopicPractice = Practice::where([
                    'practiceable_id' => $questionDetails->id,
                    'practiceable_type' => Topic::class,
                    'user_id' => Auth::user()->id,
                ])->first();

                $topic = $questionDetails->topic()
                    ->withCount(['question_details', 'quizzes'])
                    ->first();

                $totalQuestionDetailProgress;
                $totalQestionDetailsCount;
                $totalQuizProgress;
                $totalQuizzesCount;

                $totalProgress = $totalQuestionDetailProgress + $totalQuizProgress;
                $totalCount  = $totalQestionDetailsCount + $totalQuizzesCount;

                $progress;

                $progressStatus = match (true) {
                    $totalAttempts === 0 => Practice::STATUS_NOT_STARTED,
                    $progress >= 100 => Practice::STATUS_COMPLETED,
                    default => Practice::STATUS_IN_PROGRESS,
                };

                // Insert or update the practice
                Practice::updateOrCreate(
                    [
                        'practiceable_id' => $topic->id,
                        'practiceable_type' => Topic::class,
                        'user_id' => Auth::user()->id,
                    ],
                    [
                        'progress' => $progress,
                        'status' => $progressStatus,
                    ]
                );

                /* ===================================== =====================================
                     Start Course progress insert or update in practice table
                ===================================== ===================================== */

                $existingCoursePractice = Practice::where([
                    'practiceable_id' => $questionDetails->id,
                    'practiceable_type' => Course::class,
                    'user_id' => Auth::user()->id,
                ])->first();

                $course = $topic->course()
                    ->withCount(['topics'])
                    ->first();


                $totalTopicProgress;
                $totalTopicCount;

                $progress;

                $progressStatus = match (true) {
                    $totalAttempts === 0 => Practice::STATUS_NOT_STARTED,
                    $progress >= 100 => Practice::STATUS_COMPLETED,
                    default => Practice::STATUS_IN_PROGRESS,
                };

                // Insert or update the practice
                Practice::updateOrCreate(
                    [
                        'practiceable_id' => $course->id,
                        'practiceable_type' => Course::class,
                        'user_id' => Auth::user()->id,
                    ],
                    [
                        'progress' => $progress,
                        'status' => $progressStatus,
                    ]
                );
            });

            return sendResponse(true, 'Question submitted successfully', ['isAnswerCorrect' => $isAnswerCorrect], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            // Log or handle error
            return sendResponse(false, 'Failed to submit question: ' . $e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
