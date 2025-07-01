<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseTranslation extends Model
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
