<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use HasFactory;
    protected $appends = [
        'created_at_formatted',
        'updated_at_formatted',

        'created_at_human',
        'updated_at_human',
    ];

    public function creater()
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->select(['name', 'id']);
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id')->select(['name', 'id']);
    }

    // Accessor for created time
    public function getCreatedAtFormattedAttribute()
    {
        return dateTimeFormat($this->created_at);
    }

    // Accessor for updated time
    public function getUpdatedAtFormattedAttribute()
    {
        return $this->created_at != $this->updated_at ? dateTimeFormat($this->updated_at) : 'N/A';
    }

    // Accessor for created time human readable
    public function getCreatedAtHumanAttribute()
    {
        return timeFormatHuman($this->created_at);
    }

    // Accessor for updated time human readable
    public function getUpdatedAtHumanAttribute()
    {
        return $this->created_at != $this->updated_at ? timeFormatHuman($this->updated_at) : 'N/A';
    }
}
