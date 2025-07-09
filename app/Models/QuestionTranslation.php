<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionTranslation extends BaseModel
{
    protected $table = 'question_translations';

    protected $fillable = ['question_id', 'language', 'title', 'answer'];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id', 'id');
    }
}
