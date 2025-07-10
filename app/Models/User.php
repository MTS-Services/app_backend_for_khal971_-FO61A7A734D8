<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'order_index',
        'username',
        'name',
        'phone',
        'email',
        'user_class_id',
        'image',
        'dob',
        'gender',
        'country',
        'city',
        'school',
        'is_premium',
        'premium_expires_at',
        'email_verified_at',
        'otp',
        'otp_sent_at',
        'otp_expires_at',
        'password',
        'is_admin',
        'status',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'otp_sent_at' => 'datetime',
        'otp_exires_at' => 'datetime',
        'premium_expires_at' => 'datetime',
        'is_admin' => 'boolean',
        'is_premium' => 'boolean',
        'status' => 'integer',
        'gender' => 'integer',
        'user_class_id' => 'integer',
    ];


    public function practices()
    {
        return $this->hasMany(Practice::class);
    }


    protected $appends = [
        // 'created_at_formatted',
        // 'updated_at_formatted',

        // 'created_at_human',
        // 'updated_at_human',
        'status_label',
        'status_list',

        'gender_label',
        'gender_list',

    ];


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
        return $this->status ? self::getStatusList()[$this->status] : 'Unknown';
    }

    public function getStatusListAttribute(): object
    {
        return (object) self::getStatusList();
    }



    /////////////////////////
    // Gender Attributes
    /////////////////////////
    public const GENDER_MALE = 1;
    public const GENDER_FEMALE = 2;
    public const GENDER_OTHER = 3;

    public static function getGenderList(): array
    {
        return [
            self::GENDER_MALE => 'Male',
            self::GENDER_FEMALE => 'Female',
            self::GENDER_OTHER => 'Other',
        ];
    }

    public function getGenderLabelAttribute(): string
    {
        return $this->gender ? self::getGenderList()[$this->gender] : 'Unknown';
    }

    public function getGenderListAttribute(): object
    {
        return (object) self::getGenderList();
    }






    public function creater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->select(['name', 'id']);
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id')->select(['name', 'id']);
    }

    // Accessor for created time
    public function getCreatedAtFormattedAttribute(): string|null
    {
        return dateTimeFormat($this->created_at);
    }

    // Accessor for updated time
    public function getUpdatedAtFormattedAttribute(): string|null
    {
        return $this->created_at != $this->updated_at ? dateTimeFormat($this->updated_at) : null;
    }

    // Accessor for created time human readable
    public function getCreatedAtHumanAttribute(): string|null
    {
        return timeFormatHuman($this->created_at);
    }

    // Accessor for updated time human readable
    public function getUpdatedAtHumanAttribute(): string|null
    {
        return $this->created_at != $this->updated_at ? timeFormatHuman($this->updated_at) : null;
    }

    public function userClass(): BelongsTo
    {
        return $this->belongsTo(UserClass::class, 'user_class_id', 'id')->select(['id', 'name']);
    }


    public function getImageAttribute(): string|null
    {
        return $this->attributes['image'] ? asset("storage/{$this->attributes['image']}") : null;
    }


    public function isAdmin(): bool
    {
        return $this->is_admin === true;
    }

    public function isPremium(): bool
    {
        return $this->is_premium === true;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }

    public function scopeMale(Builder $query): Builder
    {
        return $query->where('gender', self::GENDER_MALE);
    }

    public function scopeFemale(Builder $query): Builder
    {
        return $query->where('gender', self::GENDER_FEMALE);
    }

    public function scopeOther(Builder $query): Builder
    {
        return $query->where('gender', self::GENDER_OTHER);
    }
    public function scopePremium(Builder $query): Builder
    {
        return $query->where('is_premium', true);
    }
    public function scopeFree(Builder $query): Builder
    {
        return $query->where('is_premium', false);
    }

    public function scopeAdmin(Builder $query): Builder
    {
        return $query->where('is_admin', true);
    }
    public function scopeUser(Builder $query): Builder
    {
        return $query->where('is_admin', false);
    }


    public function subjects(): HasManyThrough
    {
        return $this->hasManyThrough(
            Subject::class,
            UserSubject::class,
            'user_id',
            'id',
            'id',
            'subject_id'
        );
    }
}
