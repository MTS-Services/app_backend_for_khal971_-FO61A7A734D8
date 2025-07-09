<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMilestoneAchievement extends BaseModel
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
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function milestone(): BelongsTo
    {
        return $this->belongsTo(ProgressMilestone::class, 'milestone_id', 'id');
    }
    public function progress(): BelongsTo
    {
        return $this->belongsTo(UserItemProgresss::class, 'progress_id', 'id');
    }

}
