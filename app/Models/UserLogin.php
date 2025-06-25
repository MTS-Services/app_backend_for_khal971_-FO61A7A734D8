<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLogin extends Model
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
        return self::statusList()[$this->status];
    }
    public function getStatusList(): array
    {
        return self::statusList();
    }
}
