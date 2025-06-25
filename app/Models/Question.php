<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    protected $fillable = [
        'order_index',
        'topic_id',
        'question_type_id',
        'title',
        'description',
        'file',
        'points',
        'time_limit',
        'explanation',
        'hints',
        'tags',
        'status',
        'is_premium',

        'created_by',
        'updated_by',
    ];


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'status' => 'integer',
        'is_premium' => 'boolean',
        'topic_id' => 'integer',
        'question_type_id' => 'integer',
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
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }

    // Relationships
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class, 'topic_id', 'id')->withDefault();
    }

    public function questionType(): BelongsTo
    {
        return $this->belongsTo(QuestionType::class, 'question_type_id', 'id')->withDefault();
    }

    public function scopeFree(Builder $query): Builder
    {
        return $query->where('is_premium', false);
    }

    public function scopePremium(Builder $query): Builder
    {
        return $query->where('is_premium', true);
    }
}
