<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\UserMilestoneAchievementRequest;
use App\Http\Services\UserMilestoneAchievementService;
use App\Models\User;
use App\Models\UserMilestoneAchievement;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UserMilestoneAchievementController extends Controller
{
    protected UserMilestoneAchievementService $userMilestoneAchievementService;

    public function __construct(UserMilestoneAchievementService $userMilestoneAchievementService)
    {
        $this->userMilestoneAchievementService = $userMilestoneAchievementService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user_milestone_achievement = $this->userMilestoneAchievementService->getUserMilestoneAchievements()->get();
            if ($user_milestone_achievement) {
                return sendResponse(true, ' User milestone achievement list fetched successfully', $user_milestone_achievement, Response::HTTP_OK);
            } else {
                return sendResponse(false, 'Failed to fetch user milestone achievement list', null, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return sendResponse(false, 'Failed to fetch user milestone achievement list', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserMilestoneAchievementRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $user_milestone_achievement = $this->userMilestoneAchievementService->createUserMilestoneAchievement($validated);
            if (!$user_milestone_achievement) {
                return sendResponse(false, 'Failed to create user milestone achievement', null, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return sendResponse(true, ' User milestone achievement created successfully', $user_milestone_achievement, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return sendResponse(false, 'Failed to create user milestone achievement', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(UserMilestoneAchievement $user_milestone_achievement)
    {
        try {
            $user_milestone_achievement = $this->userMilestoneAchievementService->getUserMilestoneAchievement($user_milestone_achievement->id);
            if (!$user_milestone_achievement) {
                return sendResponse(false, 'Failed to fetch user milestone achievement', null, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return sendResponse(true, ' User milestone achievement fetched successfully', $user_milestone_achievement, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return sendResponse(false, 'Failed to fetch user milestone achievement', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UserMilestoneAchievementRequest   $request, UserMilestoneAchievement $user_milestone_achievement)
    {
        try{
            $validated = $request->validated();
            $user_milestone_achievement = $this->userMilestoneAchievementService->updateUserMilestoneAchievement($user_milestone_achievement, $validated);
            if (!$user_milestone_achievement) {
                return sendResponse(false, 'Failed to update user milestone achievement', null, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return sendResponse(true, ' User milestone achievement updated successfully', $user_milestone_achievement, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return sendResponse(false, 'Failed to update user milestone achievement', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserMilestoneAchievement $user_milestone_achievement)
    {
        try{
            $user_milestone_achievement = $this->userMilestoneAchievementService->deleteUserMilestoneAchievement($user_milestone_achievement);
            if (!$user_milestone_achievement) {
                return sendResponse(false, 'Failed to delete user milestone achievement', null, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return sendResponse(true, ' User milestone achievement deleted successfully', $user_milestone_achievement, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return sendResponse(false, 'Failed to delete user milestone achievement', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
