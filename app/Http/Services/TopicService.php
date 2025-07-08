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
use Illuminate\Database\Eloquent\Collection;

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
    // public function getTopics(int $course_id, string $orderBy = 'order_index', string $direction = 'asc'): Builder
    // {
    //     $query = Topic::translation($this->lang)->where('course_id', $course_id);
    //     if (!($this->user->is_premium || $this->user->is_admin)) {
    //         $query->take(12);
    //     }
    //     return $query->orderBy($orderBy, $direction)->latest();
    // }


    public function getTopics(int $course_id, string $orderBy = 'order_index', string $direction = 'asc'): Collection
    {
        $query = Topic::translation($this->lang)
            ->where('course_id', $course_id)
            ->with('course.subject')
            ->orderBy($orderBy, $direction)
            ->latest();

        if (!($this->user && ($this->user->is_premium || $this->user->is_admin))) {
            $query->take(12);
        }

        $topics = $query->get();

        if ($topics->isEmpty()) {
            return $topics;
        }

        // Fetch all user progress records for these topics
        $topicProgress = UserProgress::where('user_id', $this->user->id)
            ->where('content_type', 'topic')
            ->whereIn('content_id', $topics->pluck('id'))
            ->get()
            ->keyBy('content_id');

        // Attach progress and calculated percentage to each topic
        $topics->each(function ($topic) use ($topicProgress) {
            $progress = $topicProgress->get($topic->id);

            $topic->progress = $progress;
            $topic->is_completed = (bool)($progress && $progress->completion_percentage == 100);
            $topic->completion_percentage = $progress->completion_percentage ?? 0;
            $topic->accuracy_percentage = $progress->accuracy_percentage ?? 0;
            $topic->status = $progress->status ?? 'not_started';
        });

        return $topics;
    }


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
