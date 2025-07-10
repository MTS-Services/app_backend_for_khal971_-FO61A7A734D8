<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class QuestionDetails extends BaseModel
{
    protected $table = 'question_details';
    protected $fillable = [
        'order_index',
        'topic_id',
        'file',
        'status',

        'created_by',
        'updated_by',
    ];

    /* ==================================================================
                        Relations Start Here
      ================================================================== */

    public function translations(): HasMany
    {
        return $this->hasMany(QuestionDetailsTranslation::class, 'question_detail_id', 'id')->select('question_detail_id', 'language', 'title', 'description');
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class, 'topic_id', 'id')->with(['translations']);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function practices(): MorphMany
    {
        return $this->morphMany(Practice::class, 'practiceable');
    }

    /* ==================================================================
                        Relations End Here
      ================================================================== */

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
}
