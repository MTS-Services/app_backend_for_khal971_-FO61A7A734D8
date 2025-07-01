<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopicTranslation extends Model
{
    protected $table = 'topic_translations';
    protected $fillable = ['topic_id', 'language', 'name'];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }
}
