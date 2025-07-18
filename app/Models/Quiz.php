<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Auth;

class Quiz extends BaseModel
{
    protected $fillable = [
        'order_index',
        'topic_id',
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
    ];


    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->appends = array_merge(parent::getAppends(), [
            'status_label',
            'status_list',
        ]);
    }

    /* ================================
             Relationships Start Here
     ================================ */
    public function practice(): MorphOne
    {
        return $this->morphOne(Practice::class, 'practiceable')->where('user_id', Auth::user()->id);
    }

    public function topics()
    {
        return $this->belongsTo(Topic::class, 'topic_id', 'id')->with([
            'translations'
        ]);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(QuizTranslation::class, 'quiz_id', 'id')->select('quiz_id', 'language', 'title', 'description');
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuizOption::class);
    }
    /* ================================
             Relationships End Here
     ================================ */

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

    
    // public function translate($language): QuizTranslation|null
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
}
