<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    protected $fillable = [
        'order_index',
        'topic_id',
        'question_type_id',
        'title',
        'description',
        'file',
        'points',
        'time_limit',
        'explanation',
        'hints',
        'tags',
        'status',

        'created_by',
        'updated_by',
    ];

    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 0;

    // Relationships
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class, 'topic_id', 'id')->withDefault();
    }

    public function questionType(): BelongsTo
    {
        return $this->belongsTo(QuestionType::class, 'question_type_id', 'id')->withDefault();
    }
}
