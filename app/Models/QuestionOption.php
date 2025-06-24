<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionOption extends Model
{
    protected $fillable = 
    [
        'order_index',
        'question_id',
        'option_text',
        'is_correct',
        'explanation',
        'status',

        'created_by',
        'updated_by',
    ];

    public const STATUS_ACTIVE = '1';
    public const STATUS_INACTIVE = '0';

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id', 'id')->withDefault();
    }
}
