<?php

namespace App\Http\Services;

use App\Models\User;

class UserService
{
    protected FileService $fileService;

    public function __construct(FileService $fileService)
    {
        return $this->fileService = $fileService;
    }
    public function getUser($pararm, string $query_field = 'id'): User
    {
        return User::where($query_field, $pararm)->first();

    }

    public function getUsers($orderBy = 'name', $order = 'asc')
    {
        return User::orderBy($orderBy, $order)->latest();
    }
    public function updateUser(User $user, array $data , $file): User
    {
        $user->update($data);
        if($file){
            $data['image'] = $this->fileService->uploadFile($file, 'users', $data['name']);
            $this->fileService->fileDelete($user->image);
        }
        return $user->refresh();
    }

}
