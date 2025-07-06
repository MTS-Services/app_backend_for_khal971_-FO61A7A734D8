<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizOptionTranslation extends Model
{
    protected $table = 'quiz_option_translations';
    protected $fillable =[
        'quiz_option_id',
        'language',
        'title',
        
        'created_by',
        'updated_by',
    ];

    public function quiz_option(): BelongsTo
    {
        return $this->belongsTo(QuizOption::class);
    }
}
