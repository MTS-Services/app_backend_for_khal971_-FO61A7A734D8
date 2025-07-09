<?php

namespace App\Models;

use App\Models\BaseModel;

class Practice extends BaseModel
{
    protected $fillable = [
        'user_id',
        'practiceable_id',
        'practiceable_type',
        'attempts',
        'status',
    ];

    // Relations 
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function practiceable()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeOfUser($query, $user)
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeOfStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOfAttempts($query, $attempts)
    {
        return $query->where('attempts', $attempts);
    }

    public function scopeOfPracticeableId($query, $id)
    {
        if ($id) {
            return $query->where('practiceable_id', $id);
        } else {
            return $query;
        }
    }
    public function scopeOfPracticeableType($query, $type)
    {
        if ($type) {
            return $query->where('practiceable_type', $type);
        } else {
            return $query;
        }
    }


    // Constructor for appends
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->appends = array_merge(parent::getAppends(), [
            'status_label',
            'status_list',
        ]);
    }

    // Constants
    public const STATUS_NOT_STARTED = 'not_started';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';

    // Helpers
    public static function getStatusList(): array
    {
        return [
            self::STATUS_NOT_STARTED => 'Not Started',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_FAILED => 'Failed',
        ];
    }

    // Attributes
    public function getStatusLabelAttribute(): string
    {
        return self::getStatusList()[$this->status];
    }
    public function getStatusListAttribute(): array
    {
        return self::getStatusList();
    }
}
