<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends BaseModel
{
    protected $fillable = [
        'order_index',
        'user_id',
        'subscription_id',
        'amount',
        'currency',
        'payment_method',
        'payment_id',
        'status',
        'receipt_url',
        'notes',

        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'amount' => 'integer',
        'status' => 'integer',
    ];

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
    // [default: 'pending', note: 'pending, completed, failed']
    public const STATUS_PENDING = 0;
    public const STATUS_COMPLETED = 1;
    public const STATUS_FAILED = 2;

    public static function getStatusList(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_FAILED => 'Failed',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }


}
