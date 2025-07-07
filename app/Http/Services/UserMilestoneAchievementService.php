<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\UserMilestoneAchievement;
use Illuminate\Support\Facades\Auth;

class UserMilestoneAchievementService
{
    private User $user;
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->user = Auth::user();
    }
    public function getUserMilestoneAchievements(string $orderBy = 'order_index', string $direction = 'asc')
    {

        $query = UserMilestoneAchievement::query();
        return $query->orderBy($orderBy, $direction)->latest();
    }
    public function getUserMilestoneAchievement($param, string $query_field = 'id'): UserMilestoneAchievement|null
    {

        $query = UserMilestoneAchievement::query();
        $userMilestoneAchievement = $query->where($query_field, $param)->first();
        return $userMilestoneAchievement;
    }

    public function createUserMilestoneAchievement($data): UserMilestoneAchievement|null
    {
        $data['created_by'] = $this->user->id;
        $userMilestoneAchievement = UserMilestoneAchievement::create($data);
        return $userMilestoneAchievement;
    }
    public function updateUserMilestoneAchievement(UserMilestoneAchievement $userMilestoneAchievement, $data): UserMilestoneAchievement|null
    {
        $data['updated_by'] = $this->user->id;
        $userMilestoneAchievement->update($data);
        return $userMilestoneAchievement;
    }
    public function deleteUserMilestoneAchievement(UserMilestoneAchievement $userMilestoneAchievement): bool
    {
        return $userMilestoneAchievement->delete();
    }

}
