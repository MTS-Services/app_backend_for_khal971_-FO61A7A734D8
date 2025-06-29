<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionOptionTranslation extends Model
{
    protected $table = 'question_option_translations';
    protected $fillable =
    [
        'question_option_id',
        'language',
        'option_text',
        'explanation'
    ];

    public function questionOption(): BelongsTo
    {
        return $this->belongsTo(QuestionOption::class, 'question_option_id', 'id');
    }
}
