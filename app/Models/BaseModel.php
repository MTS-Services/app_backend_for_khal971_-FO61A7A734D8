<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BaseModel extends Model
{
    use HasFactory;
    protected $appends = [
        'created_at_formatted',
        'updated_at_formatted',

        'created_at_human',
        'updated_at_human',
    ];

    public function creater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->select(['name', 'id']);
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id')->select(['name', 'id']);
    }

    // Accessor for created time
    public function getCreatedAtFormattedAttribute(): string
    {
        return dateTimeFormat($this->created_at);
    }

    // Accessor for updated time
    public function getUpdatedAtFormattedAttribute(): string
    {
        return $this->created_at != $this->updated_at ? dateTimeFormat($this->updated_at) : 'N/A';
    }

    // Accessor for created time human readable
    public function getCreatedAtHumanAttribute(): string
    {
        return timeFormatHuman($this->created_at);
    }

    // Accessor for updated time human readable
    public function getUpdatedAtHumanAttribute(): string
    {
        return $this->created_at != $this->updated_at ? timeFormatHuman($this->updated_at) : 'N/A';
    }
}
