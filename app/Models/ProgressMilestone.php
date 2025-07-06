<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgressMilestone extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'order_index',
        'threshold_value',
        'badge_icon',
        'points_reward',
        'is_active',
        'order_index',
    ];

    protected $casts = [
        'threshold_value' => 'decimal:2',
        'points_reward' => 'integer',
        'is_active' => 'boolean',
        'order_index' => 'integer',
    ];


    /**
     * Get available content types
     */
  

    /**
     * Scope to get only active milestones
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by order_index
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order_index');
    }

    /**
     * Check if milestone has a badge reward
     */
    public function hasBadge(): bool
    {
        return !empty($this->badge_name) || !empty($this->badge_icon);
    }

    /**
     * Check if milestone has points reward
     */
    public function hasPointsReward(): bool
    {
        return $this->points_reward > 0;
    }



     public function translations(): HasMany
    {
        return $this->hasMany(ProgressMilestoneTranslation::class, 'progress_milestone_id', 'id')->select('progress_milestone_id', 'language', 'content_type', 'milestone_type', 'requirement_description', 'title', 'description', 'celebration_message', 'badge_name');
    }

    public function translate($language): SubjectTranslation|null
    {
        return $this->translations->where('language', $language)->first();
    }

    public function scopeTranslation(Builder $query, $lang): Builder
    {
        return $query->with([
            'translations' => fn($q) => $q->where('language', $lang)
        ]);
    }

    public function loadTranslation($lang)
    {
        return $this->load([
            'translations' => fn($q) => $q->where('language', $lang)
        ]);
    }
}
