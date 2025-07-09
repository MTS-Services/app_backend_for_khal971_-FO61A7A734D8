<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgressMilestoneTranslation extends BaseModel
{

    protected $fillable = [
        'progress_milestone_id',
        'language',
        'content_type',
        'milestone_type',
        'requirement_description',
        'title',
        'description',
        'celebration_message',
        'badge_name',
    ];

    // Constants for content types
    const CONTENT_TYPE_SUBJECT = 'subject';
    const CONTENT_TYPE_COURSE = 'course';
    const CONTENT_TYPE_TOPIC = 'topic';
    const CONTENT_TYPE_OVERALL = 'overall';

    // Constants for milestone types
    const MILESTONE_TYPE_COMPLETION = 'completion';
    const MILESTONE_TYPE_ACCURACY = 'accuracy';
    const MILESTONE_TYPE_STREAK = 'streak';
    const MILESTONE_TYPE_TIME_SPENT = 'time_spent';

    /**
     * Get available content types
     */
    public static function getContentTypes(): array
    {
        return [
            self::CONTENT_TYPE_SUBJECT,
            self::CONTENT_TYPE_COURSE,
            self::CONTENT_TYPE_TOPIC,
            self::CONTENT_TYPE_OVERALL,
        ];
    }

    /**
     * Get available milestone types
     */
    public static function getMilestoneTypes(): array
    {
        return [
            self::MILESTONE_TYPE_COMPLETION,
            self::MILESTONE_TYPE_ACCURACY,
            self::MILESTONE_TYPE_STREAK,
            self::MILESTONE_TYPE_TIME_SPENT,
        ];
    }

    

    /**
     * Scope to filter by content type
     */
    public function scopeByContentType(Builder $query, string $contentType): Builder
    {
        return $query->where('content_type', $contentType);
    }

    /**
     * Scope to filter by milestone type
     */
    public function scopeByMilestoneType(Builder $query, string $milestoneType): Builder
    {
        return $query->where('milestone_type', $milestoneType);
    }

    /**
     * Get formatted threshold value based on milestone type
     */
    public function getFormattedThresholdAttribute(): string
    {
        switch ($this->milestone_type) {
            case self::MILESTONE_TYPE_COMPLETION:
            case self::MILESTONE_TYPE_ACCURACY:
                return $this->threshold_value . '%';
            case self::MILESTONE_TYPE_STREAK:
                return $this->threshold_value . ' days';
            case self::MILESTONE_TYPE_TIME_SPENT:
                return $this->threshold_value . ' hours';
            default:
                return (string) $this->threshold_value;
        }
    }

    ////////////////////////////////////////
    ///////// Relationships ///////////////
    //////////////////////////////////////

    public function progressMilestone(): BelongsTo
    {
        return $this->belongsTo(ProgressMilestone::class, 'progress_milestone_id', 'id');
    }
}
