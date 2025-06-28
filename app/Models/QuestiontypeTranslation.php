<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionTypeTranslation extends Model
{
    protected $table = 'question_type_translations';
    protected $fillable = ['question_type_id', 'language', 'name', 'description'];

    public function questionType(): BelongsTo
    {
        return $this->belongsTo(QuestionType::class);
    }
}
