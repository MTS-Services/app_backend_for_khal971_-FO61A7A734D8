<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubjectTranslation extends BaseModel
{
    protected $table = 'subject_translations';

    protected $fillable = ['subject_id', 'language', 'name'];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }
}
