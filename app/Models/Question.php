<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\BaseModel;

class Question extends BaseModel
{
    protected $fillable = [
        'order_index',
        'question_details_id',
        'status',

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
        'topic_id' => 'integer',
        // 'hints' => 'array',
        // 'tags' => 'array',
    ];


    /* ==================================================================
                        Relations Start Here
      ================================================================== */

    public function translations(): HasMany
    {
        return $this->hasMany(QuestionTranslation::class, 'question_id', 'id')->select('question_id', 'language', 'title', 'answer');
    }

    public function questionDetails(): BelongsTo
    {
        return $this->belongsTo(QuestionDetails::class, 'question_details_id', 'id')->with([
            'translations' => fn($query) => $query->where('language', request()->header('Accept-Language', defaultLang())),
        ]);
    }

    /* ==================================================================
                        Relations End Here
      ================================================================== */

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->appends = array_merge(parent::getAppends(), [
            'status_label',
            // 'hints',
            // 'tags',
        ]);
    }

    /////////////////////////
    // JSON Attributes
    /////////////////////////
    // public function getHintsAttribute($value)
    // {
    //     return json_decode($value, true);
    // }

    // public function getTagsAttribute($value)
    // {
    //     return json_decode($value, true);
    // }

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
        return array_key_exists($this->status, self::getStatusList()) ? self::getStatusList()[$this->status] : 'Unknown';
    }

    public function getStatusListAttribute(): object
    {
        return (object) self::getStatusList();
    }
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }
    public function translate($language): QuestionTranslation|null
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
