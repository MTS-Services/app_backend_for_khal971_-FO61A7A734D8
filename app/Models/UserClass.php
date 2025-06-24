<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class UserClass extends BaseModel
{
    protected $table = 'user_classes';

    protected $fillable = [
        'order_index',
        'name',
        'icon',
        'status',

        'created_by',
        'updated_by',
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
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'user_class_id', 'id');
    }
}
