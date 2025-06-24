<?php

namespace App\Models;

class Subject extends BaseModel
{
    protected $fillable =
    [
        'order_index',
        'name',
        'icon',
        'status',

        'created_by',
        'updated_by',
    ];
    // Modified icon
    // public function getIconAttribute()
    // {
    //     return $this->icon ? asset('storage/' . $this->icon) : null;
    // }

}
