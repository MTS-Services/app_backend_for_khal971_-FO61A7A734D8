<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class UserProgress extends BaseModel
{

    protected $table = 'user_progress';

    protected $fillable = [
        'user_id',
        'content_type',
        'content_id',
        'total_items',
        'completed_items',
        'correct_items',
        'completion_percentage',
        'accuracy_percentage',
        'total_time_spent',
        'average_time_per_item',
        'status',
        'current_streak',
        'first_accessed_at',
        'last_accessed_at',
        'completed_at',
        'best_streak',
        'last_activity_date',
    ];

    protected $casts = [
        'completion_percentage' => 'float',
        'accuracy_percentage' => 'float',
        'total_time_spent' => 'integer',
        'average_time_per_item' => 'float',
        'current_streak' => 'integer',
        'first_accessed_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'completed_at' => 'datetime',
        'last_activity_date' => 'date',
    ];

    /* ==================================================================
                        Relations Start Here
      ================================================================== */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function userItemProgress(): HasMany
    {
        return $this->hasMany(UserItemProgresss::class, 'parent_progress_id');
    }

    /* ==================================================================
                        Relations End Here
      ================================================================== */

    /* ******************************************************************
                        Attributes Start Here
      ****************************************************************** */

    public function progressStatusText(): Attribute
    {
        return Attribute::make(
            get: function () {
                return match (true) {
                    $this->completion_percentage == 100 => 'Completed',
                    $this->completion_percentage >= 75 => 'Almost Done',
                    $this->completion_percentage >= 50 => 'Half Way',
                    $this->completion_percentage >= 25 => 'Getting Started',
                    $this->completion_percentage > 0 => 'Just Started',
                    default => 'Not Started'
                };
            }
        );
    }

    public function remainingQuestions(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->total_items - $this->completed_items
        );
    }
    /* ******************************************************************
                        Attributes End Here
      ****************************************************************** */

    public const STATUS_NOT_STARTED = '0';
    public const STATUS_IN_PROGRESS = '1';
    public const STATUS_COMPLETED = '2';
    public const STATUS_MASTERED = '3';

    public static function getStatusList(): array
    {
        return [
            self::STATUS_NOT_STARTED => 'Not Started',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_MASTERED => 'Mastered',
        ];
    }

    public const CONTENT_TYPES = [
        'question' => 'question',
        'topic' => 'topic',
        'course' => 'course',
        'subject' => 'subject',
        'quiz' => 'quiz',
        'question_set' => 'question_set',
    ];
}
