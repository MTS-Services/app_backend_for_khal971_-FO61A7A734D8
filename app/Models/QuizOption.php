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


    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->appends = array_merge(parent::getAppends(), [
            'is_correct_label',
            'is_correct_list',
        ]);
    }

    ///////////////////////////
    // Status Attributes /////
    /////////////////////////
    public const IS_CORRECT_TRUE = 1;
    public const IS_CORRECT_FALSE = 0;

    public function getIsCorrectList(): array
    {
        return [
            self::IS_CORRECT_TRUE => 'True',
            self::IS_CORRECT_FALSE => 'False',
        ];
    }
    public function getIsCorrectLabelAttribute(): string
    {
        return self::getIsCorrectList()[$this->is_correct] ?? 'Unknown';
    }
    public function getIsCorrectListAttribute(): object
    {
        return (object) self::getIsCorrectList();
    }

    /////////////////////////////
    // Start Relationships /////
    ///////////////////////////

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

    ////////////////////////
    // End Relationships //
    //////////////////////
}
