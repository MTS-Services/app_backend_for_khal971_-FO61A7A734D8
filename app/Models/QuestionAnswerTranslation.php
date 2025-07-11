<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionAnswerTranslation extends BaseModel
{
    protected $table = 'question_answer_translations';
    protected $fillable =
        [
            'question_answer_id',
            'language',
            'answer',
            'match_percentage'
        ];
    public function questionAnswer(): BelongsTo
    {
        return $this->belongsTo(QuestionAnswer::class, 'question_answer_id', 'id');
    }
}
