<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserClassTranslation extends Model
{
    protected $table = 'user_class_translations';
    protected $fillable = ['user_class_id', 'language', 'name'];

    public function userClass()
    {
        return $this->belongsTo(UserClass::class);
    }
}
