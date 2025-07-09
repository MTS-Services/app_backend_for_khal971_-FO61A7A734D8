<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\UserClass;
use App\Jobs\TranslateModelJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\UserClassTranslation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class UserClassService
{
    private User $user;
    protected $lang;
    public function __construct()
    {
        $this->user = Auth::user();
        $this->lang = request()->header('Accept-Language') ?: self::getDefaultLang();
    }
    public function getDefaultLang(): string
    {
        return defaultLang() ?: 'en';
    }
    /**
     * Fetch UserClasss, optionally filtered and ordered.
     *
     * @param  string  $direction asc|desc default: asc
     * @return Builder
     */
    public function getUserClasses(string $orderBy = 'order_index', string $direction = 'asc'): Builder
    {

        $query = UserClass::query()->translation($this->lang);
        if (!($this->user->is_admin)) {
            $query->free()->take(12);
        }
        return $query->orderBy($orderBy, $direction);
    }

    public function getUserClass($param, string $query_field = 'id'): UserClass|null
    {
        $query = UserClass::query()->translation($this->lang);
        if (!($this->user->is_admin)) {
            $query->free()->take(12);
        }
        return $query->where($query_field, $param)->first();
    }
    public function createUserClass($data): UserClass|null
    {
        try {
            $data['created_by'] = $this->user->id;
            return DB::transaction(function () use ($data) {
                $user_class = UserClass::create($data);
                UserClassTranslation::create(['user_class_id' => $user_class->id, 'language' => $this->lang, 'name' => $data['name']]);
                TranslateModelJob::dispatch(UserClass::class, UserClassTranslation::class, 'user_class_id', $user_class->id, ['name'], $this->lang);
                $user_class = $user_class->refresh()->loadTranslation($this->lang);
                return $user_class;
            });
        } catch (\Exception $e) {
            Log::error('UserClass Create Error: ' . $e->getMessage());
            return null;
        }
    }

    public function updateUserClass(UserClass $suer_class, $data): UserClass|null
    {
        try {
            $data['updated_by'] = $this->user->id;
            return DB::transaction(function () use ($suer_class, $data) {
                $suer_class->update($data);
                UserClassTranslation::updateOrCreate(['user_class_id' => $suer_class->id, 'language' => $this->lang], ['name' => $data['name']]);
                TranslateModelJob::dispatch(UserClass::class, UserClassTranslation::class, 'user_class_id', $suer_class->id, ['name'], $this->lang);
                $suer_class = $suer_class->refresh()->loadTranslation($this->lang);
                return $suer_class;
            });
        } catch (\Exception $e) {
            Log::error('UserClass Update Error: ' . $e->getMessage());
            return null;
        }
    }

    public function deleteUserClass(UserClass $suer_class): bool
    {
        return $suer_class->delete();
    }

    public function toggleStatus(UserClass $suer_class): UserClass
    {
        $suer_class->update(['status' => !$suer_class->status, 'updated_by' => $this->user->id]);
        return $suer_class->refresh();
    }
}
