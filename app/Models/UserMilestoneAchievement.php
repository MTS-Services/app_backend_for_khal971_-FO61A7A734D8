<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMilestoneAchievement extends Model
{
     protected $fillable = [
        'user_id',
        'milestone_id',
        'progress_id',
        'achieved_value',
        'achieved_at',
        'is_notified',
        'notification_sent_at',
    ];

    protected $casts = [
        'is_notified' => 'boolean',
        'achieved_at' => 'datetime',
        'notification_sent_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withDefault();
    }
    public function milestone(): BelongsTo
    {
        return $this->belongsTo(ProgressMilestone::class, 'milestone_id', 'id')->withDefault();
    }
    public function progress(): BelongsTo
    {
        return $this->belongsTo(UserItemProgresss::class, 'progress_id', 'id')->withDefault();
    }

}
