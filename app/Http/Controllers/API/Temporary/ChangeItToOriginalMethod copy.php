<?php

namespace App\Http\Controllers\API\Temporary;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Practice;
use App\Models\Question;
use App\Models\QuestionDetails;
use App\Models\Quiz;
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

                // Eager load everything at once
                $question->load([
                    'questionDetails.topic.course.subject',
                    'questionDetails.topic.question_details:id,topic_id',
                    'questionDetails.topic.quizzes:id,topic_id',
                    'questionDetails.topic.course.topics:id,course_id'
                ]);

                /** ================== QuestionDetails Progress =================== */
                $questionDetails = $question->questionDetails()->withCount('questions')->first();
                $totalQuestions = $questionDetails->questions_count ?? 0;

                $existingQuestionDetailPractice = Practice::where([
                    'practiceable_id' => $questionDetails->id,
                    'practiceable_type' => QuestionDetails::class,
                    'user_id' => Auth::id(),
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

                Practice::updateOrCreate(
                    [
                        'practiceable_id' => $questionDetails->id,
                        'practiceable_type' => QuestionDetails::class,
                        'user_id' => Auth::id(),
                    ],
                    [
                        'total_attempts' => $totalAttempts,
                        'correct_attempts' => $correctAttempts,
                        'wrong_attempts' => $wrongAttempts,
                        'progress' => $progress,
                        'status' => $progressStatus,
                    ]
                );

                /** ================== Topic Progress =================== */
                $topic = $questionDetails->topic;

                $questionDetailIds = $topic->question_details->pluck('id');
                $quizIds = $topic->quizzes->pluck('id');

                $practices = Practice::where('user_id', Auth::id())
                    ->where(function ($q) use ($questionDetailIds, $quizIds) {
                        $q->whereIn('practiceable_id', $questionDetailIds)
                            ->where('practiceable_type', QuestionDetails::class)
                            ->orWhereIn('practiceable_id', $quizIds)
                            ->where('practiceable_type', 'App\\Models\\Quiz');
                    })->get();

                $questionDetailProgressSum = $practices->where('practiceable_type', QuestionDetails::class)->sum('progress');
                $quizProgressSum = $practices->where('practiceable_type', 'App\\Models\\Quiz')->sum('progress');

                $questionDetailCount = $topic->question_details->count();
                $quizCount = $topic->quizzes->count();
                $totalTopicUnits = $questionDetailCount + $quizCount;

                $topicProgress = $totalTopicUnits > 0
                    ? min(100, round(($questionDetailProgressSum + $quizProgressSum) / $totalTopicUnits, 2))
                    : 0;

                $topicStatus = match (true) {
                    $topicProgress == 0 => Practice::STATUS_NOT_STARTED,
                    $topicProgress >= 100 => Practice::STATUS_COMPLETED,
                    default => Practice::STATUS_IN_PROGRESS,
                };

                Practice::updateOrCreate(
                    [
                        'practiceable_id' => $topic->id,
                        'practiceable_type' => Topic::class,
                        'user_id' => Auth::id(),
                    ],
                    [
                        'progress' => $topicProgress,
                        'status' => $topicStatus,
                    ]
                );

                /** ================== Course Progress =================== */
                $course = $topic->course;
                $topicIds = $course->topics->pluck('id');

                $topicPractices = Practice::where('user_id', Auth::id())
                    ->whereIn('practiceable_id', $topicIds)
                    ->where('practiceable_type', Topic::class)
                    ->get();

                $topicProgressSum = $topicPractices->sum('progress');
                $topicCount = $course->topics->count();

                $courseProgress = $topicCount > 0
                    ? min(100, round($topicProgressSum / $topicCount, 2))
                    : 0;

                $courseStatus = match (true) {
                    $courseProgress == 0 => Practice::STATUS_NOT_STARTED,
                    $courseProgress >= 100 => Practice::STATUS_COMPLETED,
                    default => Practice::STATUS_IN_PROGRESS,
                };

                Practice::updateOrCreate(
                    [
                        'practiceable_id' => $course->id,
                        'practiceable_type' => Course::class,
                        'user_id' => Auth::id(),
                    ],
                    [
                        'progress' => $courseProgress,
                        'status' => $courseStatus,
                    ]
                );
            });

            return sendResponse(true, 'Question submitted successfully', ['isAnswerCorrect' => $isAnswerCorrect], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return sendResponse(false, 'Failed to submit question: ' . $e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function quizSubmit(Request $request, int $id)
    {
        try {
            $isAnswerCorrect = false;

            DB::transaction(function () use ($id, $request, &$isAnswerCorrect) {
                $userId = Auth::id();

                // Eager load required relations
                $quiz = Quiz::with([
                    'topics.course.topics:id,course_id',
                    'topics.quizzes:id,topic_id'
                ])->find($id);

                if (!$quiz) {
                    throw new \Exception('Quiz not found');
                }

                /** ================== Random Answer Simulation =================== */
                $isAnswerCorrect = (bool) random_int(0, 1);

                /** ================== Quiz Progress Update =================== */
                $existingQuizPractice = Practice::where([
                    'practiceable_id' => $quiz->id,
                    'practiceable_type' => Quiz::class,
                    'user_id' => $userId,
                ])->first();

                $totalAttempts = ($existingQuizPractice->total_attempts ?? 0) + 1;
                $correctAttempts = ($existingQuizPractice->correct_attempts ?? 0) + ($isAnswerCorrect ? 1 : 0);
                $wrongAttempts = ($existingQuizPractice->wrong_attempts ?? 0) + (!$isAnswerCorrect ? 1 : 0);

                $progress = 100;
                $status = Practice::STATUS_COMPLETED;

                Practice::updateOrCreate(
                    [
                        'practiceable_id' => $quiz->id,
                        'practiceable_type' => Quiz::class,
                        'user_id' => $userId,
                    ],
                    [
                        'total_attempts' => $totalAttempts,
                        'correct_attempts' => $correctAttempts,
                        'wrong_attempts' => $wrongAttempts,
                        'progress' => $progress,
                        'status' => $status,
                    ]
                );

                /** ================== Topic Progress Update =================== */
                $topic = $quiz->topics;
                $quizIds = $topic->quizzes->pluck('id');
                $questionDetailIds = $topic->question_details->pluck('id');

                $topicPractices = Practice::where('user_id', $userId)
                    ->where(function ($q) use ($questionDetailIds, $quizIds) {
                        $q->where(function ($qq) use ($questionDetailIds) {
                            $qq->whereIn('practiceable_id', $questionDetailIds)
                                ->where('practiceable_type', QuestionDetails::class);
                        })->orWhere(function ($qq) use ($quizIds) {
                            $qq->whereIn('practiceable_id', $quizIds)
                                ->where('practiceable_type', Quiz::class);
                        });
                    })->get();

                $questionDetailProgressSum = $topicPractices->where('practiceable_type', QuestionDetails::class)->sum('progress');
                $quizProgressSum = $topicPractices->where('practiceable_type', Quiz::class)->sum('progress');

                $quizCount = $topic->quizzes->count();
                $questionDetailCount = $topic->question_details->count();
                $topicTotalUnits = $quizCount + $questionDetailCount;

                $topicProgress = $topicTotalUnits > 0
                    ? min(100, round(($quizProgressSum + $questionDetailProgressSum) / $topicTotalUnits, 2))
                    : 0;

                $topicStatus = match (true) {
                    $topicProgress == 0 => Practice::STATUS_NOT_STARTED,
                    $topicProgress >= 100 => Practice::STATUS_COMPLETED,
                    default => Practice::STATUS_IN_PROGRESS,
                };

                Practice::updateOrCreate(
                    [
                        'practiceable_id' => $topic->id,
                        'practiceable_type' => Topic::class,
                        'user_id' => $userId,
                    ],
                    [
                        'progress' => $topicProgress,
                        'status' => $topicStatus,
                    ]
                );

                /** ================== Course Progress Update =================== */
                $course = $topic->course;
                $topicIds = $course->topics->pluck('id');

                $coursePractices = Practice::where('user_id', $userId)
                    ->whereIn('practiceable_id', $topicIds)
                    ->where('practiceable_type', Course::class)
                    ->get();

                $courseProgressSum = $coursePractices->sum('progress');
                $courseTopicCount = $course->topics->count();

                $courseProgress = $courseTopicCount > 0
                    ? min(100, round($courseProgressSum / $courseTopicCount, 2))
                    : 0;

                $courseStatus = match (true) {
                    $courseProgress == 0 => Practice::STATUS_NOT_STARTED,
                    $courseProgress >= 100 => Practice::STATUS_COMPLETED,
                    default => Practice::STATUS_IN_PROGRESS,
                };

                Practice::updateOrCreate(
                    [
                        'practiceable_id' => $course->id,
                        'practiceable_type' => Course::class,
                        'user_id' => $userId,
                    ],
                    [
                        'progress' => $courseProgress,
                        'status' => $courseStatus,
                    ]
                );
            });

            return sendResponse(true, 'Quiz submitted successfully', ['isAnswerCorrect' => $isAnswerCorrect], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return sendResponse(false, 'Failed to submit quiz: ' . $e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
