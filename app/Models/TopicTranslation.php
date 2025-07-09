<?php

namespace App\Models;

use App\Models\BaseModel;

class TopicTranslation extends BaseModel
{
    protected $table = 'topic_translations';
    protected $fillable = ['topic_id', 'language', 'name'];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }
}
