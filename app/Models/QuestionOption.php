<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionOption extends BaseModel
{
    protected $fillable =
    [
        'order_index',
        'question_id',
        'is_correct',
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
        'is_correct' => 'boolean',
        'question_id' => 'integer',
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
        return array_key_exists($this->status, self::getStatusList()) ? self::getStatusList()[$this->status] : 'Unknown';
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

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id', 'id')->withDefault();
    }

    public function scopeCorrect(Builder $query): Builder
    {
        return $query->where('is_correct', true);
    }

    public function scopeIncorrect(Builder $query): Builder
    {
        return $query->where('is_correct', false);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(QuestionOptionTranslation::class, 'question_option_id', 'id')->select('question_option_id', 'language', 'option_text', 'explanation');
    }

    public function translate($language): QuestionOptionTranslation|null
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
