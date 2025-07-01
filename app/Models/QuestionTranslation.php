<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionTranslation extends Model
{
    protected $table = 'question_translations';

    protected $fillable = ['question_option_id', 'language', 'title', 'description', 'point', 'time_limit', 'explanation'];

    public function questionOption(): BelongsTo
    {
        return $this->belongsTo(QuestionOption::class, 'question_option_id', 'id');
    }
}
