<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizTranslation extends BaseModel
{
    protected $table = 'quizzes_translations';
    protected $fillable =[
        'quiz_id',
        'language',
        'title',
        'description',
    ];

    public function Quiz(): BelongsTo
    {
        return $this->belongsTo( Quiz::class, 'quiz_id', 'id');
    }
}
