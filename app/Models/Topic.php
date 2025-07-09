<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Topic extends BaseModel
{
    protected $fillable =
        [
            'order_index',
            'course_id',
            'status',

            'created_by',
            'updated_by'

        ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'status' => 'integer',
        'course_id' => 'integer',
    ];

    /* ==================================================================
                        Relations Start Here
      ================================================================== */

    public function translations(): HasMany
    {
        return $this->hasMany(TopicTranslation::class, 'topic_id', 'id')->select('topic_id', 'language', 'name');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id', 'id')->with(['translations']);
    }

    public function question_details(): HasMany
    {
        return $this->hasMany(QuestionDetails::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    // public function userProgress(): HasMany
    // {
    //     return $this->hasMany(UserProgress::class, 'content_id')
    //         ->where('content_type', 'topic');
    // }

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
    // public function scopeFree(Builder $query): Builder
    // {
    //     return $query->where('is_premium', false);
    // }

    // public function scopePremium(Builder $query): Builder
    // {
    //     return $query->where('is_premium', true);
    // }
    // public function translate($language): SubjectTranslation|null
    // {
    //     return $this->translations->where('language', $language)->first();
    // }

    // public function scopeTranslation(Builder $query, $lang): Builder
    // {
    //     return $query->with([
    //         'translations' => fn($q) => $q->where('language', $lang)
    //     ]);
    // }

    // public function loadTranslation($lang)
    // {
    //     return $this->load([
    //         'translations' => fn($q) => $q->where('language', $lang)
    //     ]);
    // }


    public function scopeCounts(Builder $query): Builder
    {
        return $query->withCount([
            'quizzes',
            'question_details'
        ]);
    }
}
