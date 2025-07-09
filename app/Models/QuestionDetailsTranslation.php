<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionDetailsTranslation extends BaseModel
{
    protected $table = 'question_details_translations';
    protected $fillable = [
        'question_detail_id',
        'language',
        'title',
        'description'
    ];

    public function question_details(): BelongsTo
    {
        return $this->belongsTo(QuestionDetails::class, 'question_detail_id', 'id');
    }
}
