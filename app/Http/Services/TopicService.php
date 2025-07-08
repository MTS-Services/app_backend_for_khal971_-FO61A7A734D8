<?php

namespace App\Http\Services;

use App\Jobs\TranslateModelJob;
use App\Models\Topic;
use App\Models\TopicTranslation;
use App\Models\User;
use App\Models\UserProgress;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TopicService
{
    private User $user;
    protected $lang;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->lang = request()->header('Accept-Language') ?: self::getDefaultLang();
    }
    public function getDefaultLang(): string
    {
        return defaultLang() ?: 'en';
    }
    /**
     * Fetch Topics, optionally filtered and ordered.
     *
     * @param  string  $direction asc|desc default: asc
     * @return Builder
     */
    public function getTopics(int $course_id, string $orderBy = 'order_index', string $direction = 'asc'): Builder
    {
        $query = Topic::translation($this->lang)->where('course_id', $course_id);
        if (!($this->user->is_premium || $this->user->is_admin)) {
            $query->take(12);
        }
        $query['porgress'] = $this->getTopicsProgress($course_id)->get();
        return $query->orderBy($orderBy, $direction)->latest();
    }

    private function getTopicsProgress(int $course_id)
    {
        $query = UserProgress::where('content_type', 'topic')
            ->where('content_id', $course_id)
            ->where('user_id', $this->user->id);
        return $query;
    }

    private function getTopicProgress(int $topic_id): Builder
    {
        $query = UserProgress::where('content_type', 'topic')
            ->where('content_id', $topic_id)
            ->where('user_id', $this->user->id);
        return $query;
    }

    // public function getTopicProgress(int $userId, int $topicId): JsonResponse
    // {
    //     try {
    //         $progress = UserProgress::with([
    //             'topic.course.subject' // Eager load topic, course, and subject
    //         ])
    //         ->where('user_id', $userId)
    //         ->where('content_type', 'topic')
    //         ->where('content_id', $topicId)
    //         ->first();

    //         if (!$progress) {
    //             $topic = Topic::with(['course.subject', 'questionDetails.questions'])->find($topicId);
    //             $totalQuestions = $topic?->questionDetails()->withCount('questions')->get()->sum('questions_count') ?? 0;

    //             return response()->json([
    //                 'success' => true,
    //                 'data' => [
    //                     'user_id' => $userId,
    //                     'topic_id' => $topicId,
    //                     'total_questions' => $totalQuestions,
    //                     'completed_questions' => 0,
    //                     'correct_questions' => 0,
    //                     'completion_percentage' => 0,
    //                     'accuracy_percentage' => 0,
    //                     'status' => 'not_started',
    //                     'progress_status_text' => 'Not Started',
    //                     'topic_name' => $topic?->name ?? 'Unknown Topic',
    //                     'course_name' => $topic?->course?->name ?? 'Unknown Course',
    //                     'subject_name' => $topic?->course?->subject?->name ?? 'Unknown Subject',
    //                     'remaining_questions' => $totalQuestions,
    //                 ],
    //                 'message' => 'No progress found, showing default state'
    //             ]);
    //         }

    //         $data = [
    //             'id' => $progress->id,
    //             'user_id' => $progress->user_id,
    //             'topic_id' => $progress->content_id,
    //             'total_questions' => $progress->total_items,
    //             'completed_questions' => $progress->completed_items,
    //             'correct_questions' => $progress->correct_items,
    //             'completion_percentage' => $progress->completion_percentage,
    //             'accuracy_percentage' => $progress->accuracy_percentage,
    //             'total_time_spent' => $progress->total_time_spent,
    //             'average_time_per_item' => $progress->average_time_per_item,
    //             'status' => $progress->status,
    //             'current_streak' => $progress->current_streak,
    //             'first_accessed_at' => $progress->first_accessed_at,
    //             'last_accessed_at' => $progress->last_accessed_at,
    //             'completed_at' => $progress->completed_at,
    //             'topic_name' => $progress->topic?->name,
    //             'course_name' => $progress->topic?->course?->name,
    //             'subject_name' => $progress->topic?->course?->subject?->name,
    //             'remaining_questions' => $progress->total_items - $progress->completed_items,
    //             'progress_status_text' => $this->getCompletionStatusText($progress->completion_percentage), // Helper method
    //         ];

    //         return response()->json([
    //             'success' => true,
    //             'data' => $data
    //         ]);

    //     } catch (\Exception $e) {
    //         Log::error('Error fetching topic progress: ' . $e->getMessage());
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Error fetching topic progress',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function getTopic($param, string $query_field = 'id'): Topic|null
    {
        $query = Topic::translation($this->lang);
        if (!($this->user->is_premium || $this->user->is_admin)) {
            $query->take(12);
        }
        return $query->where($query_field, $param)->first();
    }
    public function createTopic($data): Topic|null
    {
        try {
            $data['created_by'] = $this->user->id;
            return DB::transaction(function () use ($data) {
                $topic = Topic::create($data);
                TopicTranslation::create(['topic_id' => $topic->id, 'language' => $this->lang, 'name' => $data['name']]);
                TranslateModelJob::dispatch(Topic::class, TopicTranslation::class, 'topic_id', $topic->id, ['name'], $this->lang);
                $topic = $topic->refresh()->loadTranslation($this->lang);
                return $topic;
            });
        } catch (\Exception $e) {
            Log::error('Topic Create Error: ' . $e->getMessage());
            return null;
        }
    }

    public function updateTopic(Topic $topic, $data): Topic|null
    {
        try {
            $data['updated_by'] = $this->user->id;
            return DB::transaction(function () use ($topic, $data) {
                $topic->update($data);
                TopicTranslation::updateOrCreate(['topic_id' => $topic->id, 'language' => $this->lang], ['name' => $data['name']]);
                TranslateModelJob::dispatch(Topic::class, TopicTranslation::class, 'topic_id', $topic->id, ['name'], $this->lang);
                $topic = $topic->refresh()->loadTranslation($this->lang);
                return $topic;
            });
        } catch (\Exception $e) {
            Log::error('Topic Update Error: ' . $e->getMessage());
            return null;
        }
    }

    public function deleteTopic(Topic $topic): bool
    {
        return $topic->delete();
    }

    public function toggleStatus(Topic $topic): Topic|null
    {
        $topic->update(['status' => !$topic->status, 'updated_by' => $this->user->id]);
        return $topic->refresh();
    }
}
