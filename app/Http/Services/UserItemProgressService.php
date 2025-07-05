<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\UserItemProgresss;
use App\Models\UserProgress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class UserItemProgressService
{
    private User $user;
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->user = Auth::user();
    }

    public function getUserItemProgress($param, string $query_field = 'id'): UserItemProgresss|null
    {
        $user_item_progress = UserItemProgresss::query();
        if ($this->user->is_admin) {
            $user_item_progress = $user_item_progress->where($query_field, $param);
        } else {
            $user_item_progress = $user_item_progress->where($query_field, $param)->where('user_id', $this->user->id);
        }
        $user_item_progress = $user_item_progress->first();
        if ($user_item_progress) {
            return $user_item_progress;
        }
        return null;
    }
    public function list(array $filters = [], int $perPage = 15)
    {
        $query = UserItemProgresss::query();

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['item_type'])) {
            $query->where('item_type', $filters['item_type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['parent_progress_id'])) {
            $query->where('parent_progress_id', $filters['parent_progress_id']);
        }

        return $query->orderBy('updated_at', 'desc')->paginate($perPage);
    }

    public function createUserItemProgress(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['user_id'] = $this->user->id;
            $data['created_by'] = $this->user->id;
            return UserItemProgresss::create($data);
        });
    }

    public function updateUserItemProgress($id, array $data)
    {
        $data['updated_by'] = $this->user->id;
        return DB::transaction(function () use ($id, $data) {
            return UserItemProgresss::where('id', $id)->update($data);
        });
    }
}
