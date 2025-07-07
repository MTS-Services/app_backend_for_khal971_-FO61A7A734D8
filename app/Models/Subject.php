<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends BaseModel
{
    protected $fillable =
    [
        'order_index',
        // 'name',
        'icon',
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
    ];


    /* ==================================================================
                        Relations Start Here
      ================================================================== */

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'subject_id', 'id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(SubjectTranslation::class, 'subject_id', 'id')->select('subject_id', 'language', 'name');
    }
    /* ==================================================================
                        Relations End Here
      ================================================================== */


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
        return self::getStatusList()[$this->status] ?? 'Unknown';
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

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }

    public function scopeFree(Builder $query): Builder
    {
        return $query->where('is_premium', false);
    }

    public function scopePremium(Builder $query): Builder
    {
        return $query->where('is_premium', true);
    }

    public function translate($language): SubjectTranslation|null
    {
        return $this->translations->where('language', $language)->first();
    }

    public function scopeTranslation(Builder $query, $lang): Builder
    {
        return $query->with([
            'translations' => fn($q) => $q->where('language', $lang)
        ]);
    }

    public function loadTranslation($lang)
    {
        return $this->load([
            'translations' => fn($q) => $q->where('language', $lang)
        ]);
    }
}
