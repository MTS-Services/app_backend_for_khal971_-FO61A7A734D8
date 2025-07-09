<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseTranslation extends BaseModel
{
    protected $table = 'course_translations';
    
    protected $fillable = [
        'course_id',
        'language',
        'name',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
