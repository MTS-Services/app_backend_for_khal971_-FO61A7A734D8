<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class UserLogin extends BaseModel
{
    protected $fillable = [
        'order_index',
        'user_id',
        'ip',
        'country',
        'city',
        'region',
        'lat',
        'lon',
        'device',
        'browser',
        'platform',
        'status',
        'last_login_at',
        'device_id',
        'user_agent',

    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->appends = array_merge(parent::getAppends(), [

            'status_label',
        ]);
    }
    protected $casts = [
        'status' => 'integer',
        'user_id' => 'integer',
        'order_index' => 'integer',
    ];

    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 0;
    public static function statusList(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
        ];
    }
    public function getStatusLabelAttribute()
    {
        return $this->status ? self::getStatusList()[$this->status] : 'Unknown';
    }
    public function getStatusList(): array
    {
        return self::statusList();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function scopeSelf(Builder $query): Builder
    {
        return $query->where('user_id', Auth::id());
    }
}
