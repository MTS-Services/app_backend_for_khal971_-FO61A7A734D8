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


    public function getTopics(int $course_id, string $orderBy = 'order_index', string $direction = 'asc'): Collection
    {
        $query = Topic::counts()->with(['translations', 'course.subject', 'practice'])
            ->where('course_id', $course_id)
            ->orderBy($orderBy, $direction)
            ->latest();

        if (!($this->user && ($this->user->is_premium || $this->user->is_admin))) {
            $query->take(12);
        }
        $topics = $query->get();
        return $topics;
    }


    public function getTopic($param, string $query_field = 'id'): Topic|null
    {
        $query = Topic::counts()->with(['translations', 'course.subject']);
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
                $topic = $topic->refresh()->load([
                    'translations',
                    'course.subject'
                ]);
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
                $topic = $topic->refresh()->load([
                    'translations',
                    'course.subject'
                ]);
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
