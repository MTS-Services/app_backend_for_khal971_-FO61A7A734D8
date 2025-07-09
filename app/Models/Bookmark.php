<?php

namespace App\Models;

use App\Models\BaseModel;

class Bookmark extends BaseModel
{
    protected $fillable = [
        'user_id',
        'bookmarkable_id',
        'bookmarkable_type',
    ];


    // Relations 
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookmarkable()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeOfUser($query, $user)
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeOfBookmarkableId($query, $id)
    {
        if ($id) {
            return $query->where('bookmarkable_id', $id);
        } else {
            return $query;
        }
    }
    public function scopeOfBookmarkableType($query, $type)
    {
        if ($type) {
            return $query->where('bookmarkable_type', $type);
        } else {
            return $query;
        }
    }
}
