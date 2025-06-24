<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable =
    [
        'order_index',
        'subject_id',
        'name',
        'status',

        'created_by',
        'updated_by'

    ];
}
