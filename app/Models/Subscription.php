<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends BaseModel
{
    protected $fillable = [
        'order_index',
        'user_id',
        'plan_id',
        'name',
        'stripe_id',
        'stripe_status',
        'stripe_price_id',
        'stripe_product_id',
        'apple_transaction_id',
        'google_order_id',
        'quantity',
        'starts_at',
        'ends_at',
        'canceled_at',
        'payment_method',
        'payment_frequency',
        'status',

        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * The "booted" method of the model.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->appends = array_merge(parent::getAppends(), [
            'status_label',
            // 'status_list',
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

    public function getStatusListAttribute(): object
    {
        return (object) self::getStatusList();
    }
}
