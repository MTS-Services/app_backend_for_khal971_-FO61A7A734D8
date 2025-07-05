<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProgress extends Model
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
        'first_accessed_at',
        'last_accessed_at',
        'completed_at',
        'current_streak',
        'best_streak',
        'last_activity_date',
    ];

    protected $casts = [
        'first_accessed_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'completed_at' => 'datetime',
        'last_activity_date' => 'date',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public const STATUS_NOT_STARTED = 'not_started';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_MASTERED = 'mastered';
}
