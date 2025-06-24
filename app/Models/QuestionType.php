<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionType extends Model
{
    protected $fillable = 
    [
        'order_index',
        'name',
        'description',
        'status',

        'created_by',
        'updated_by',
    ];

    public function __construct()
    {
        //
    }
    public function questions()
    {
        return $this->hasMany(Question::class, 'question_type_id', 'id');
    }

}
