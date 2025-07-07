<?php

namespace App\Http\Services;

use App\Jobs\TranslateModelJob;
use App\Models\ProgressMilestone;
use App\Models\ProgressMilestoneTranslation;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProgressMilestoneService
{
    protected FileService $fileService;
    private User $user;
    protected $lang;
    /**
     * Create a new class instance.
     */
    public function __construct(FileService $fileService)
    {
        $this->user = Auth::user();
        $this->lang = request()->header('Accept-Language') ?: self::getDefaultLang();
        $this->fileService = $fileService;
    }
    public function getDefaultLang()
    {
        return defaultLang() ?: 'en';
    }

    public function getProgressMilestones(string $orderBy = 'order_index', string $direction = 'asc')
    {
        $query = ProgressMilestone::translation($this->lang)->ordered();
        return $query->orderBy($orderBy, $direction)->latest();
    }
    public function getProgressMilestone($param, string $query_field = 'id'): ProgressMilestone|null
    {
        $query = ProgressMilestone::translation($this->lang)->ordered();
        $milestone = $query->where($query_field, $param)->first();
        return $milestone;
    }
    /**
     * Create a new milestone
     */
    public function createMilestone($data, $file = null): ProgressMilestone|null
    {
        try {
            $data['created_by'] = $this->user->id;
            if ($file) {
                $data['badge_icon'] = $this->fileService->uploadFile($file, 'milestones', $data['content_type']);
            }
            return DB::transaction(function () use ($data) {
                $milestone = ProgressMilestone::create($data);
                ProgressMilestoneTranslation::create([
                    'progress_milestone_id' => $milestone->id,
                    'language' => $this->lang,
                    'content_type' => $data['content_type'] ?? '',
                    'milestone_type' => $data['milestone_type'] ?? '',
                    'requirement_description' => $data['requirement_description']?? '',
                    'title' => $data['title'] ?? '',
                    'description' => $data['description'] ?? null,
                    'celebration_message' => $data['celebration_message'] ?? null,
                    'badge_name' => $data['badge_name'] ?? null,

                ]);
                TranslateModelJob::dispatch(ProgressMilestone::class, ProgressMilestoneTranslation::class, 'progress_milestone_id', $milestone->id, ['content_type', 'milestone_type', 'requirement_description', 'title', 'description', 'celebration_message', 'badge_name'], $this->lang);
                $milestone = $milestone->refresh()->loadTranslation($this->lang);
                return $milestone;
            });
        } catch (\Exception $e) {
            Log::error('Milestone Create Error: ' . $e->getMessage());
            return null;
        }
    }

    public function updateMilestone(ProgressMilestone $milestone, $data , $file = null): ProgressMilestone|null
    {
        try{
            $data['updated_by'] = $this->user->id;
            if ($file) {
                $data['badge_icon'] = $this->fileService->uploadFile($file, 'milestones', $data['content_type']);
                $this->fileService->fileDelete($milestone->badge_icon);
            }
            return DB::transaction(function () use ($milestone, $data) {
                $milestone->update($data);
                ProgressMilestoneTranslation::where('progress_milestone_id', $milestone->id)->where('language', $this->lang)->update([
                    'content_type' => $data['content_type'] ?? '',
                    'milestone_type' => $data['milestone_type'] ?? '',
                    'requirement_description' => $data['requirement_description']?? '',
                    'title' => $data['title'] ?? '',
                    'description' => $data['description'] ?? null,
                    'celebration_message' => $data['celebration_message'] ?? null,
                    'badge_name' => $data['badge_name'] ?? null,
                ]);
                TranslateModelJob::dispatch(ProgressMilestone::class, ProgressMilestoneTranslation::class, 'progress_milestone_id', $milestone->id, ['content_type', 'milestone_type', 'requirement_description', 'title', 'description', 'celebration_message', 'badge_name'], $this->lang);
                $milestone = $milestone->refresh()->loadTranslation($this->lang);
                return $milestone;
            });
        } catch (\Exception $e) {
            Log::error('Milestone Update Error: ' . $e->getMessage());
            return null;
        }
    }





    /**
     * Get all active milestones
     */
    public function getActiveMilestones(): Collection
    {
        return ProgressMilestone::active()
            ->ordered()
            ->get();
    }

    /**
     * Get milestones with pagination
     */
    public function getPaginatedMilestones(int $perPage = 15): LengthAwarePaginator
    {
        return ProgressMilestone::active()
            ->ordered()
            ->paginate($perPage);
    }

    /**
     * Get milestones by content type
     */
    public function getMilestonesByContentType(string $contentType): Collection
    {
        return ProgressMilestone::active()
            ->byContentType($contentType)
            ->ordered()
            ->get();
    }

    /**
     * Get milestones by milestone type
     */
    public function getMilestonesByMilestoneType(string $milestoneType): Collection
    {
        return ProgressMilestone::active()
            ->byMilestoneType($milestoneType)
            ->ordered()
            ->get();
    }


    /**
     * Delete a milestone (soft delete by setting is_active to false)
     */
    public function deleteMilestone(ProgressMilestone $milestone): bool
    {
        return $milestone->update(['is_active' => false]);
    }

    /**
     * Hard delete a milestone
     */
    public function hardDeleteMilestone(ProgressMilestone $milestone): bool
    {
        return $milestone->delete();
    }

    /**
     * Reorder milestones
     */
    public function reorderMilestones(array $milestoneIds): bool
    {
        try {
            foreach ($milestoneIds as $index => $milestoneId) {
                ProgressMilestone::where('id', $milestoneId)
                    ->update(['order_index' => $index]);
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if a user has achieved a milestone
     */
    public function checkMilestoneAchievement(ProgressMilestone $milestone, float $userProgress): bool
    {
        return $userProgress >= $milestone->threshold_value;
    }

    /**
     * Get milestones that a user has achieved
     */
    public function getAchievedMilestones(array $userProgress): Collection
    {
        $achievedMilestones = collect();

        foreach ($userProgress as $contentType => $progressData) {
            $milestones = $this->getMilestonesByContentType($contentType);

            foreach ($milestones as $milestone) {
                $progressKey = $milestone->milestone_type;
                $userValue = $progressData[$progressKey] ?? 0;

                if ($this->checkMilestoneAchievement($milestone, $userValue)) {
                    $achievedMilestones->push($milestone);
                }
            }
        }

        return $achievedMilestones;
    }

    /**
     * Get the next available order index
     */
    private function getNextOrderIndex(): int
    {
        return ProgressMilestone::max('order_index') + 1;
    }

    /**
     * Get milestone statistics
     */
    public function getMilestoneStats(): array
    {
        $total = ProgressMilestone::count();
        $active = ProgressMilestone::active()->count();
        $inactive = $total - $active;

        $byContentType = ProgressMilestone::active()
            ->selectRaw('content_type, COUNT(*) as count')
            ->groupBy('content_type')
            ->pluck('count', 'content_type')
            ->toArray();

        $byMilestoneType = ProgressMilestone::active()
            ->selectRaw('milestone_type, COUNT(*) as count')
            ->groupBy('milestone_type')
            ->pluck('count', 'milestone_type')
            ->toArray();

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'by_content_type' => $byContentType,
            'by_milestone_type' => $byMilestoneType,
        ];
    }
}
