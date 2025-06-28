<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionTranslation extends Model
{
    protected $table = 'question_translations';

    protected $fillable = ['question_id', 'language', 'title', 'description', 'point', 'time_limit', 'explanation'];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id', 'id');
    }
}
