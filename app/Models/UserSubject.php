<?php

namespace App\Models;


class UserSubject extends BaseModel
{
    protected $fillable = [
        'order_index',
        'user_id',
        'subject_id',
        'created_by',
        'updated_by',
    ];

    // cast
    public $casts = [
        'order_index' => 'integer',
        'subject_id' => 'array',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
