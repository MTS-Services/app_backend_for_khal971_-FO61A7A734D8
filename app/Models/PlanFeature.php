<?php

namespace App\Models;

use App\Models\BaseModel;

class PlanFeature extends BaseModel
{
    protected $fillable = [
        'order_index',
        'plan_id',
        'name',
        'icon',
        'description',
        'status',
        
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->appends = array_merge(parent::getAppends(), [
            'status_label',
        ]);
    }



    /////////////////////////
    // Status Attributes
    /////////////////////////
    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 0;

    public static function getStatusList(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::getStatusList()[$this->status];
    }

    public function getStatusListAttribute(): array
    {
        return self::getStatusList();
    }

    // Modified icon
    public function getIconAttribute(): string|null
    {
        return $this->attributes['icon'] ? asset("storage/{$this->attributes['icon']}") : null;
    }
}
