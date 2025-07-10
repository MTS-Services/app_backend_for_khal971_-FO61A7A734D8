<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanTranslation extends Model
{
    protected $table = 'plan_translations';
    protected $fillable = [
        'plan_id',
        'language',
        'name',
        'description'
    ];

    public function plan(): BelongsTo   
    {
        return $this->belongsTo(Plan::class, 'plan_id', 'id');
    }
}
