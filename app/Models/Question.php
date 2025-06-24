<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'order_index',
        'topic_id',
        'question_type_id',
        'title',
        'description',
        'file',
        'points',
        'time_limit',
        'explanation',
        'hints',
        'tags',
        'status',

        'created_by',
        'updated_by',
    ];

    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 0;
}
