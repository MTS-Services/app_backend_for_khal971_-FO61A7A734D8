<?php

namespace App\Http\Services;

use App\Models\User;
use Auth;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    public function getUser($pararm, string $query_field = 'id'): User
    {
        return User::where($query_field, $pararm)->first();

    }

    public function getUsers($orderBy = 'name', $order = 'asc')
    {
        return User::orderBy($orderBy, $order)->latest();
    }


}
