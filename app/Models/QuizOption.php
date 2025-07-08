<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizOption extends BaseModel
{
    protected $table = 'quiz_options';
    protected $fillable = ['quiz_id', 'order_index', 'is_correct'];

    public const IS_CORRECT_TRUE = 1;
    public const IS_CORRECT_FALSE = 0;

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class, 'quiz_id', 'id')->with([
            'translations' => fn($query) => $query->where('language', request()->header('Accept-Language', self::getDefaultLang())),
        ]);
    }
     public function translations(): HasMany
    {
        return $this->hasMany(QuizOptionTranslation::class, 'quiz_option_id', 'id')->select('quiz_option_id', 'language', 'title');
    }

    public function translate($language): QuizTranslation|null
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
