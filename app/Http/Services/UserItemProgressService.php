<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\UserItemProgresss;
use App\Models\UserItemProgressss;
use App\Models\UserProgress;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class UserItemProgressService
// {
//     private User $user;
//     /**
//      * Create a new class instance.
//      */
//     public function __construct()
//     {
//         $this->user = Auth::user();
//     }

//     public function getUserItemProgresss($param, string $query_field = 'id'): UserItemProgressss|null
//     {
//         $user_item_progress = UserItemProgressss::query();
//         if ($this->user->is_admin) {
//             $user_item_progress = $user_item_progress->where($query_field, $param);
//         } else {
//             $user_item_progress = $user_item_progress->where($query_field, $param)->where('user_id', $this->user->id);
//         }
//         $user_item_progress = $user_item_progress->first();
//         if ($user_item_progress) {
//             return $user_item_progress;
//         }
//         return null;
//     }
//     public function list(array $filters = [], int $perPage = 15)
//     {
//         $query = UserItemProgressss::query();

//         if (!empty($filters['user_id'])) {
//             $query->where('user_id', $filters['user_id']);
//         }

//         if (!empty($filters['item_type'])) {
//             $query->where('item_type', $filters['item_type']);
//         }

//         if (!empty($filters['status'])) {
//             $query->where('status', $filters['status']);
//         }

//         if (!empty($filters['parent_progress_id'])) {
//             $query->where('parent_progress_id', $filters['parent_progress_id']);
//         }

//         return $query->orderBy('updated_at', 'desc')->paginate($perPage);
//     }

//     public function createUserItemProgresss(array $data)
//     {
//         return DB::transaction(function () use ($data) {
//             $data['user_id'] = $this->user->id;
//             $data['created_by'] = $this->user->id;
//             return UserItemProgressss::create($data);
//         });
//     }

//     public function updateUserItemProgresss($id, array $data)
//     {
//         $data['updated_by'] = $this->user->id;
//         return DB::transaction(function () use ($id, $data) {
//             return UserItemProgressss::where('id', $id)->update($data);
//         });
//     }
// }


{
    /**
     * Create or update user item progress.
     */
    public function createOrUpdate(array $data): UserItemProgresss
    {
        DB::beginTransaction();

        try {
            $progress = UserItemProgresss::updateOrCreate(
                [
                    'user_id' => $data['user_id'],
                    'item_type' => $data['item_type'],
                    'item_id' => $data['item_id'],
                ],
                $data
            );

            // Update timestamps based on status
            $this->updateTimestamps($progress, $data);

            DB::commit();

            return $progress->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create/update user item progress', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get user progress for specific item.
     */
    public function getUserItemProgresss(int $userId, string $itemType, int $itemId): ?UserItemProgresss
    {
        return UserItemProgresss::where([
            'user_id' => $userId,
            'item_type' => $itemType,
            'item_id' => $itemId,
        ])->first();
    }

    /**
     * Get user progress for multiple items.
     */
    public function getUserProgressForItems(int $userId, array $items): Collection
    {
        $conditions = collect($items)->map(function ($item) use ($userId) {
            return [
                'user_id' => $userId,
                'item_type' => $item['type'],
                'item_id' => $item['id'],
            ];
        });

        return UserItemProgresss::where(function ($query) use ($conditions) {
            foreach ($conditions as $condition) {
                $query->orWhere($condition);
            }
        })->get();
    }

    /**
     * Get paginated user progress.
     */
    public function getUserItemProgress(int $userId, array $filters = [], int $perPage = 2): LengthAwarePaginator
    {
        $query = UserItemProgresss::forUser($userId)
            ->with(['user', 'parentProgress'])
            ->orderBy('item_order')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if (!empty($filters['item_type'])) {
            $query->forItemType($filters['item_type']);
        }

        if (!empty($filters['status'])) {
            $query->withStatus($filters['status']);
        }

        if (!empty($filters['parent_progress_id'])) {
            $query->where('parent_progress_id', $filters['parent_progress_id']);
        }

        if (isset($filters['is_bookmarked']) && $filters['is_bookmarked']) {
            $query->bookmarked();
        }

        if (isset($filters['is_flagged']) && $filters['is_flagged']) {
            $query->where('is_flagged', true);
        }

        return $query->paginate($perPage);
    }

    /**
     * Update progress status.
     */
    public function updateStatus(int $progressId, int $status, array $additionalData = []): UserItemProgresss
    {
        $progress = UserItemProgresss::findOrFail($progressId);

        DB::beginTransaction();

        try {
            $updateData = array_merge(['status' => $status], $additionalData);

            // Handle status-specific logic
            switch ($status) {
                case UserItemProgresss::STATUS_VIEWED:
                    if (!$progress->first_accessed_at) {
                        $updateData['first_accessed_at'] = now();
                    }
                    $updateData['last_accessed_at'] = now();
                    break;

                case UserItemProgresss::STATUS_ATTEMPTED:
                    $updateData['attempts'] = $progress->attempts + 1;
                    $updateData['last_accessed_at'] = now();
                    break;

                case UserItemProgresss::STATUS_CORRECT:
                    $updateData['attempts'] = $progress->attempts + 1;
                    $updateData['correct_attempts'] = $progress->correct_attempts + 1;
                    $updateData['completed_at'] = now();
                    $updateData['last_accessed_at'] = now();
                    break;

                case UserItemProgresss::STATUS_INCORRECT:
                    $updateData['attempts'] = $progress->attempts + 1;
                    $updateData['last_accessed_at'] = now();
                    break;

                case UserItemProgresss::STATUS_COMPLETED:
                    $updateData['completed_at'] = now();
                    $updateData['last_accessed_at'] = now();
                    break;
            }

            $progress->update($updateData);

            DB::commit();

            return $progress->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update progress status', [
                'progress_id' => $progressId,
                'status' => $status,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Add time spent to progress.
     */
    public function addTimeSpent(int $progressId, int $seconds): UserItemProgresss
    {
        $progress = UserItemProgresss::findOrFail($progressId);

        $progress->update([
            'time_spent' => $progress->time_spent + $seconds,
            'last_accessed_at' => now(),
        ]);

        return $progress->fresh();
    }

    /**
     * Toggle bookmark status.
     */
    public function toggleBookmark(int $progressId): UserItemProgresss
    {
        $progress = UserItemProgresss::findOrFail($progressId);

        $progress->update([
            'is_bookmarked' => !$progress->is_bookmarked,
        ]);

        return $progress->fresh();
    }

    /**
     * Toggle flag status.
     */
    public function toggleFlag(int $progressId): UserItemProgresss
    {
        $progress = UserItemProgresss::findOrFail($progressId);

        $progress->update([
            'is_flagged' => !$progress->is_flagged,
        ]);

        return $progress->fresh();
    }

    /**
     * Update notes for progress.
     */
    public function updateNotes(int $progressId, string $notes): UserItemProgresss
    {
        $progress = UserItemProgresss::findOrFail($progressId);

        $progress->update(['notes' => $notes]);

        return $progress->fresh();
    }

    /**
     * Get progress statistics for a user.
     */
    public function getProgressStatistics(int $userId, array $filters = []): array
    {
        $query = UserItemProgresss::forUser($userId);

        // Apply filters
        if (!empty($filters['item_type'])) {
            $query->forItemType($filters['item_type']);
        }

        if (!empty($filters['parent_progress_id'])) {
            $query->where('parent_progress_id', $filters['parent_progress_id']);
        }

        $stats = $query->selectRaw('
            COUNT(*) as total_items,
            SUM(CASE WHEN status IN ("completed", "correct") THEN 1 ELSE 0 END) as completed_items,
            SUM(attempts) as total_attempts,
            SUM(correct_attempts) as total_correct_attempts,
            SUM(time_spent) as total_time_spent,
            AVG(score) as average_score,
            COUNT(CASE WHEN is_bookmarked = 1 THEN 1 END) as bookmarked_items,
            COUNT(CASE WHEN is_flagged = 1 THEN 1 END) as flagged_items
        ')->first();

        return [
            'total_items' => $stats->total_items ?? 0,
            'completed_items' => $stats->completed_items ?? 0,
            'completion_percentage' => $stats->total_items > 0 ?
                round(($stats->completed_items / $stats->total_items) * 100, 2) : 0,
            'total_attempts' => $stats->total_attempts ?? 0,
            'total_correct_attempts' => $stats->total_correct_attempts ?? 0,
            'accuracy_percentage' => $stats->total_attempts > 0 ?
                round(($stats->total_correct_attempts / $stats->total_attempts) * 100, 2) : 0,
            'total_time_spent' => $stats->total_time_spent ?? 0,
            'average_score' => $stats->average_score ? round($stats->average_score, 2) : 0,
            'bookmarked_items' => $stats->bookmarked_items ?? 0,
            'flagged_items' => $stats->flagged_items ?? 0,
        ];
    }

    /**
     * Bulk update progress for multiple items.
     */
    public function bulkUpdateProgress(int $userId, array $items): Collection
    {
        DB::beginTransaction();

        try {
            $updatedItems = collect();

            foreach ($items as $item) {
                $progress = $this->createOrUpdate(array_merge($item, ['user_id' => $userId]));
                $updatedItems->push($progress);
            }

            DB::commit();

            return $updatedItems;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to bulk update progress', [
                'user_id' => $userId,
                'items_count' => count($items),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete progress record.
     */
    public function deleteProgress(int $progressId): bool
    {
        return UserItemProgresss::destroy($progressId) > 0;
    }

    /**
     * Get progress by parent progress ID.
     */
    public function getProgressByParent(int $parentProgressId): Collection
    {
        return UserItemProgresss::where('parent_progress_id', $parentProgressId)
            ->orderBy('item_order')
            ->get();
    }

    /**
     * Update timestamps based on status and data.
     */
    private function updateTimestamps(UserItemProgresss $progress, array $data): void
    {
        $updates = [];

        if (!$progress->first_accessed_at && isset($data['status']) && $data['status'] !== UserItemProgresss::STATUS_NOT_STARTED) {
            $updates['first_accessed_at'] = now();
        }

        if (isset($data['status']) && $data['status'] !== UserItemProgresss::STATUS_NOT_STARTED) {
            $updates['last_accessed_at'] = now();
        }

        if (isset($data['status']) && in_array($data['status'], [UserItemProgresss::STATUS_COMPLETED, UserItemProgresss::STATUS_CORRECT])) {
            $updates['completed_at'] = now();
        }

        if (!empty($updates)) {
            $progress->update($updates);
        }
    }
}
