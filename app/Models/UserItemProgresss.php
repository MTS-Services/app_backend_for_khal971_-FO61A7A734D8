<?php

namespace App\Models;


class UserItemProgresss extends BaseModel
{
    protected $table = 'user_item_progresses';

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
        'is_bookmarked' => 'boolean',
        'is_flagged' => 'boolean',
        'first_accessed_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public const STATUS_NOT_STARTED = 1;
    public const STATUS_IN_PROGRESS = 2;
    public const STATUS_COMPLETED = 3;
    public const STATUS_MASTERED = 4;

    public static function getStatusList(): array
    {
        return [
            self::STATUS_NOT_STARTED => 'Not Started',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_MASTERED => 'Mastered',
        ];
    }

    public function getStatusListAttribute(): array
    {
        return self::getStatusList();
    }
}
