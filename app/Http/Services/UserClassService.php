<?php

namespace App\Http\Services;

use App\Models\UserClass;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class UserClassService
{
    private User $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }
    /**
     * Fetch UserClasss, optionally filtered and ordered.
     *
     * @param  string  $direction asc|desc default: asc
     * @return Builder
     */
    public function getUserClasses(string $orderBy = 'order_index', string $direction = 'asc')
    {
        $query = UserClass::query();
        return $query->orderBy($orderBy, $direction)->latest();

    }

    public function getUserClass($param, string $query_field = 'id'): UserClass|null
    {
        $query = UserClass::where($query_field, $param)->first();
        return $query;
    }
    public function createUserClass($data): UserClass
    {
        $data['created_by'] = $this->user->id;
        return UserClass::create($data)->refresh();
    }

    public function updateUserClass(UserClass $suer_class, $data): UserClass
    {
        $data['updated_by'] = $this->user->id;
        $suer_class->update($data);
        return $suer_class->refresh();
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
