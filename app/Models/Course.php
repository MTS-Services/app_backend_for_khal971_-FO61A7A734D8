<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\BaseModel;

class Course extends BaseModel
{
    protected $fillable =
    [
        'order_index',
        'subject_id',
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
    ];

    /* ==================================================================
                        Relations Start Here
      ================================================================== */

    public function translations(): HasMany
    {
        return $this->hasMany(CourseTranslation::class, 'course_id', 'id')->select('course_id', 'language', 'name');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class)->with([
            'translations' => fn($query) => $query->where('language', request()->header('Accept-Language', self::getDefaultLang())),
        ]);
    }

    public function practices()
    {
        return $this->morphMany(Practice::class, 'practiceable')->with([
            'translations' => fn($query) => $query->where('language', request()->header('Accept-Language', self::getDefaultLang())),
        ]);
    }

    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class, 'course_id', 'id')->with([
            'translations' => fn($query) => $query->where('language', request()->header('Accept-Language', self::getDefaultLang())),
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
    public function translate($language): CourseTranslation|null
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

    public function scopeCounts(Builder $query): Builder
    {
        return $query->withCount([
            'topics',
            'topics as questions_count' => function (Builder $query) {
                $query->join('question_details', 'topics.id', '=', 'question_details.topic_id')
                    ->selectRaw('count(question_details.id)');
            },

            'topics as quizzes_count' => function (Builder $query) {
                $query->join('quizzes', 'topics.id', '=', 'quizzes.topic_id')
                    ->selectRaw('count(quizzes.id)');
            },
        ]);
    }
}
