<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class UserItemProgresss extends BaseModel
{
    protected $table = 'user_item_progress';

    protected $fillable = [
        'user_id',
        'parent_progress_id',
        'item_type',
        'item_id',
        'item_order',
        'status',
        'attempts',
        'correct_attempts',
        'time_spent',
        'first_accessed_at',
        'last_accessed_at',
        'completed_at',
        'score',
        'is_bookmarked',
        'is_flagged',
        'notes',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'parent_progress_id' => 'integer',
        'item_id' => 'integer',
        'item_order' => 'integer',
        'attempts' => 'integer',
        'correct_attempts' => 'integer',
        'time_spent' => 'integer',
        'first_accessed_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_bookmarked' => 'boolean',
        'is_flagged' => 'boolean',
        'score' => 'float',
    ];

    /* ==================================================================
                        Relations Start Here
      ================================================================== */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parentProgress(): BelongsTo
    {
        return $this->belongsTo(UserProgress::class, 'parent_progress_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'item_id');
    }

    /* ==================================================================
                        Relations End Here
      ================================================================== */

    /* ******************************************************************
                        Attributes Start Here
      ****************************************************************** */

    public function progressPercentage(): Attribute
    {
        return Attribute::make(
            get: function () {
                return match ($this->status) {
                    'completed', 'correct' => 100.0,
                    'attempted', 'incorrect' => 50.0,
                    'viewed' => 25.0,
                    default => 0.0
                };
            }
        );
    }

    public function successRate(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->attempts > 0 ? ($this->correct_attempts / $this->attempts) * 100 : 0
        );
    }

    public function progressIcon(): Attribute
    {
        return Attribute::make(
            get: function () {
                return match ($this->status) {
                    'correct' => '✓',
                    'incorrect' => '✗',
                    'attempted' => '⚠',
                    'viewed' => '👁',
                    default => '○'
                };
            }
        );
    }

    /* ******************************************************************
                        Attributes End Here
      ****************************************************************** */

    // Status constants
    const STATUS_NOT_STARTED = 0;
    const STATUS_VIEWED = 1;
    const STATUS_ATTEMPTED = 2;
    const STATUS_COMPLETED = 3;
    const STATUS_CORRECT = 4;
    const STATUS_INCORRECT = 5;
    const STATUS_SKIPPED = 6;

    // Item type constants
    const ITEM_TYPE_QUESTION = 'question';
    const ITEM_TYPE_LESSON = 'lesson';
    const ITEM_TYPE_VIDEO = 'video';
    const ITEM_TYPE_QUIZ = 'quiz';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_NOT_STARTED,
            self::STATUS_VIEWED,
            self::STATUS_ATTEMPTED,
            self::STATUS_COMPLETED,
            self::STATUS_CORRECT,
            self::STATUS_INCORRECT,
            self::STATUS_SKIPPED,
        ];
    }

    public static function getStatusLabels(): array
    {
        return [
            self::STATUS_NOT_STARTED => 'not_started',
            self::STATUS_VIEWED => 'viewed',
            self::STATUS_ATTEMPTED => 'attempted',
            self::STATUS_COMPLETED => 'completed',
            self::STATUS_CORRECT => 'correct',
            self::STATUS_INCORRECT => 'incorrect',
            self::STATUS_SKIPPED => 'skipped',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::getStatusLabels()[$this->status] ?? 'unknown';
    }

    public static function getItemTypes(): array
    {
        return [
            self::ITEM_TYPE_QUESTION,
            self::ITEM_TYPE_LESSON,
            self::ITEM_TYPE_VIDEO,
            self::ITEM_TYPE_QUIZ,
        ];
    }

    /**
     * Scope a query to only include records for a specific user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include records for a specific item type.
     */
    public function scopeForItemType($query, string $itemType)
    {
        return $query->where('item_type', $itemType);
    }

    /**
     * Scope a query to only include records with a specific status.
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include completed items.
     */
    public function scopeCompleted($query)
    {
        return $query->whereIn('status', [self::STATUS_COMPLETED, self::STATUS_CORRECT]);
    }

    /**
     * Scope a query to only include bookmarked items.
     */
    public function scopeBookmarked($query)
    {
        return $query->where('is_bookmarked', true);
    }

    /**
     * Check if the item is completed.
     */
    public function isCompleted(): bool
    {
        return in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_CORRECT]);
    }

    /**
     * Calculate completion percentage.
     */
    public function getCompletionPercentage(): float
    {
        if ($this->attempts === 0) {
            return 0;
        }

        return ($this->correct_attempts / $this->attempts) * 100;
    }
}
