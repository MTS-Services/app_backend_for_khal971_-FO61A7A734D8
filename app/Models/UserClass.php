<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserClass extends BaseModel
{
    protected $table = 'user_classes';

    protected $fillable = [
        'order_index',
        'name',
        'status',
        'created_by',
        'updated_by',
    ];


    
}
