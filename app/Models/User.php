<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'otp_sent_at' => 'datetime',
            'otp_exires_at' => 'datetime',
            'premium_expires_at' => 'datetime',
            'is_admin' => 'boolean',
        ];
    }



    protected $appends = [
        'created_at_formatted',
        'updated_at_formatted',

        'created_at_human',
        'updated_at_human',
    ];

    public function creater()
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->select(['name', 'id']);
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id')->select(['name', 'id']);
    }

    // Accessor for created time
    public function getCreatedAtFormattedAttribute()
    {
        return dateTimeFormat($this->created_at);
    }

    // Accessor for updated time
    public function getUpdatedAtFormattedAttribute()
    {
        return $this->created_at != $this->updated_at ? dateTimeFormat($this->updated_at) : 'N/A';
    }

    // Accessor for created time human readable
    public function getCreatedAtHumanAttribute()
    {
        return timeFormatHuman($this->created_at);
    }

    // Accessor for updated time human readable
    public function getUpdatedAtHumanAttribute()
    {
        return $this->created_at != $this->updated_at ? timeFormatHuman($this->updated_at) : 'N/A';
    }

    public function userClass()
    {
        return $this->belongsTo(UserClass::class, 'user_class_id', 'id')->select(['name', 'id']);
    }


    public function getImageAttribute()
    {
        return $this->attributes['image'] ? asset("storage/{$this->attributes['image']}") : null;
    }




}
