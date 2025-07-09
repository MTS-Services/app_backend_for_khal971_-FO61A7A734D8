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
        'last_activity_date' => 'date',
        'completion_percentage' => 'float',
        'accuracy_percentage' => 'float',
        'total_time_spent' => 'integer',
        'average_time_per_item' => 'float',
        'current_streak' => 'integer',
        'first_accessed_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    /* ==================================================================
                        Relations Start Here
      ================================================================== */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class, 'content_id');
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

    public const CONTENT_TYPES = [
        'question' => 'question',
        'topic' => 'topic',
        'course' => 'course',
        'subject' => 'subject',
        'quiz' => 'quiz',
        'question_set' => 'question_set',
    ];

    public const CONTENT_TYPE_LABEL = [
        'question' => 'Question',
        'topic' => 'Topic',
        'course' => 'Course',
        'subject' => 'Subject',
        'quiz' => 'Quiz',
        'question_set' => 'Question Set',
    ];
}
