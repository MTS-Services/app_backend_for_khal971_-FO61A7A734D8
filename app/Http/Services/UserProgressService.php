<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\UserProgress;
use Illuminate\Support\Facades\Auth;

class UserProgressService
{
    private User $user;
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->user = Auth::user();
    }
    public function getUserProgress( string $direction = 'asc')
    {
        $query = UserProgress::query();
        return $query->orderBy( $direction)->latest();
    }

    public function getProgress($contentType, $contentId): ?UserProgress
    {
        $user_progress = UserProgress::query();
        if (!($this->user->is_admin)) {
            $user_progress->free()->take(12);
        }
        return $user_progress->where('user_id', $this->user->id)
            ->where('content_type', $contentType)
            ->where('content_id', $contentId)
            ->first();
    }

     public function createOrUpdateUserProgress(array $data): UserProgress
    {
        return UserProgress::updateOrCreate(
            [
                'user_id' => $this->user->id,
                'content_type' => $data['content_type'],
                'content_id' => $data['content_id'],
            ],
            [
                'total_items' => $data['total_items'] ?? 0,
                'completed_items' => $data['completed_items'] ?? 0,
                'correct_items' => $data['correct_items'] ?? 0,
                'completion_percentage' => $this->calculateCompletion($data),
                'accuracy_percentage' => $this->calculateAccuracy($data),
                'total_time_spent' => $data['total_time_spent'] ?? 0,
                'average_time_per_item' => $data['average_time_per_item'] ?? 0,
                'status' => $data['status'] ?? '1',
                'first_accessed_at' => $data['first_accessed_at'] ?? null,
                'last_accessed_at' => now(),
                'completed_at' => $data['completed_at'] ?? null,
                'current_streak' => $data['current_streak'] ?? 0,
                'best_streak' => $data['best_streak'] ?? 0,
                'last_activity_date' => now()->toDateString(),
            ]
        );
    }
    private function calculateCompletion(array $data): float
    {
        if (!isset($data['total_items']) || $data['total_items'] == 0) return 0.0;
        return round(($data['completed_items'] ?? 0) / $data['total_items'] * 100, 2);
    }

    private function calculateAccuracy(array $data): float
    {
        if (!isset($data['completed_items']) || $data['completed_items'] == 0) return 0.0;
        return round(($data['correct_items'] ?? 0) / $data['completed_items'] * 100, 2);
    }
}
