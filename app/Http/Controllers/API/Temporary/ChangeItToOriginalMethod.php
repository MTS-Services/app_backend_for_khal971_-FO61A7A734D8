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
            $isAnswerCorrect = false;

            DB::transaction(function () use ($id, &$isAnswerCorrect) {
                $question = Question::with([
                    'questionDetails.topic.course.topics',
                    'questionDetails.topic.quizzes',
                ])->findOrFail($id);

                $isAnswerCorrect = (bool) random_int(0, 1);
                $userId = Auth::id();

                $questionDetails = $question->questionDetails;
                $topic = $questionDetails->topic;
                $course = $topic->course;

                $this->updatePractice($userId, $questionDetails, QuestionDetails::class, $isAnswerCorrect, true);
                $this->updateTopicAndCourseProgress($userId, $topic, $course);
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

            DB::transaction(function () use ($id, &$isAnswerCorrect) {
                $quiz = Quiz::with([
                    'topics.course.topics',
                    'topics.quizzes',
                ])->findOrFail($id);

                $isAnswerCorrect = (bool) random_int(0, 1);
                $userId = Auth::id();
                $topic = $quiz->topics;
                $course = $topic->course;

                $this->updatePractice($userId, $quiz, Quiz::class, $isAnswerCorrect, true);
                $this->updateTopicAndCourseProgress($userId, $topic, $course);
            });

            return sendResponse(true, 'Quiz submitted successfully', ['isAnswerCorrect' => $isAnswerCorrect], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return sendResponse(false, 'Failed to submit quiz: ' . $e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function updatePractice($userId, $model, $type, $isCorrect, $addAttempt = false)
    {
        $practice = Practice::firstOrNew([
            'practiceable_id' => $model->id,
            'practiceable_type' => $type,
            'user_id' => $userId,
        ]);

        if ($addAttempt) {
            $practice->total_attempts += 1;
            $practice->correct_attempts += $isCorrect ? 1 : 0;
            $practice->wrong_attempts += $isCorrect ? 0 : 1;
        }

        $practice->progress = 100;
        $practice->status = Practice::STATUS_COMPLETED;
        $practice->save();
    }

    private function updateTopicAndCourseProgress($userId, $topic, $course)
    {
        $questionDetailIds = $topic->question_details->pluck('id');
        $quizIds = $topic->quizzes->pluck('id');

        $practices = Practice::where('user_id', $userId)
            ->where(function ($q) use ($questionDetailIds, $quizIds) {
                $q->whereIn('practiceable_id', $questionDetailIds)
                    ->where('practiceable_type', QuestionDetails::class)
                    ->orWhereIn('practiceable_id', $quizIds)
                    ->where('practiceable_type', Quiz::class);
            })->get();

        $questionDetailProgress = $practices->where('practiceable_type', QuestionDetails::class)->sum('progress');
        $quizProgress = $practices->where('practiceable_type', Quiz::class)->sum('progress');

        $totalUnits = $topic->question_details->count() + $topic->quizzes->count();
        $topicProgress = $totalUnits > 0 ? min(100, round(($questionDetailProgress + $quizProgress) / $totalUnits, 2)) : 0;
        $topicStatus = $this->getStatusFromProgress($topicProgress);

        Practice::updateOrCreate([
            'practiceable_id' => $topic->id,
            'practiceable_type' => Topic::class,
            'user_id' => $userId,
        ], [
            'progress' => $topicProgress,
            'status' => $topicStatus,
        ]);

        $topicIds = $course->topics->pluck('id');
        $coursePractices = Practice::where('user_id', $userId)
            ->whereIn('practiceable_id', $topicIds)
            ->where('practiceable_type', Topic::class)
            ->get();

        $courseProgress = $coursePractices->count() > 0
            ? min(100, round($coursePractices->sum('progress') / $coursePractices->count(), 2))
            : 0;

        $courseStatus = $this->getStatusFromProgress($courseProgress);

        Practice::updateOrCreate([
            'practiceable_id' => $course->id,
            'practiceable_type' => Course::class,
            'user_id' => $userId,
        ], [
            'progress' => $courseProgress,
            'status' => $courseStatus,
        ]);
    }

    private function getStatusFromProgress($progress)
    {
        return match (true) {
            $progress == 0 => Practice::STATUS_NOT_STARTED,
            $progress >= 100 => Practice::STATUS_COMPLETED,
            default => Practice::STATUS_IN_PROGRESS,
        };
    }
}