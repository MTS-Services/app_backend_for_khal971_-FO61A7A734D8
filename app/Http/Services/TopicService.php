<?php

namespace App\Http\Services;

use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TopicService
{
    private User $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }
    /**
     * Fetch Topics, optionally filtered and ordered.
     *
     * @param  string  $direction asc|desc default: asc
     * @return Builder
     */
    public function getTopics(string $orderBy = 'order_index', string $direction = 'asc'): Builder
    {
        $query = Topic::query();
        if (!($this->user->is_premium || $this->user->is_admin)) {
            $query->free()->take(12);
        }
        return $query->orderBy($orderBy, $direction)->latest();
    }

    public function getTopic($param, string $query_field = 'id'): Topic|null
    {
        $query = Topic::query();
        if (!($this->user->is_premium || $this->user->is_admin)) {
            $query->free()->take(12);
        }
        return $query->where($query_field, $param)->first();
    }
    public function createTopic($data): Topic
    {
        $data['created_by'] = $this->user->id;
        return Topic::create($data)->refresh();
    }

    public function updateTopic(Topic $topic, $data): Topic
    {
        $data['updated_by'] = $this->user->id;
        $topic->update($data);
        return $topic->refresh();
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
