<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanTranslation extends Model
{
    protected $table = 'plan_translations';
    protected $fillable = [
        'plan_id',
        'language',
        'name',
        'description'
    ];
}
