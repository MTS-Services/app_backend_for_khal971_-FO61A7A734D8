<?php

namespace App\Http\Services;

use App\Models\User;

class UserService
{
    protected FileService $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }
    public function getUser($pararm, string $query_field = 'id'): User|null
    {
        return User::where($query_field, $pararm)->first();

    }

    public function getUsers($orderBy = 'name', $order = 'asc')
    {
        return User::orderBy($orderBy, $order)->latest();
    }
    public function updateUser(User $user, array $data, $file): User
    {
        $data['updated_by'] = request()->user()->id;
        $user->update($data);
        if ($file) {
            $data['image'] = $this->fileService->uploadFile($file, 'users', $data['name']);
            $this->fileService->fileDelete($user->image);
        }
        return $user->refresh();
    }

    public function deleteUser(User $user): bool
    {
        return $user->delete();
    }
}
