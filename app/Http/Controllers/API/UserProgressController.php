<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\UserProgressRequest;
use App\Http\Services\UserProgressService;
use App\Models\UserProgress;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UserProgressController extends Controller
{
     protected UserProgressService $userProgressService;

    public function __construct(UserProgressService $userProgressService)
    {
        $this->userProgressService = $userProgressService;
    }

    public function userProgress(): JsonResponse
    {
      try {
        $progress = $this->userProgressService->getUserProgress()->get();

        if (!$progress) {
            return sendResponse(false, 'Failed to retrieve user progress', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return sendResponse(true, 'User progress retrieved successfully', $progress, Response::HTTP_OK);
      } catch (\Exception $e) {
        Log::error('UserProgress Fetch Error: ' . $e->getMessage());
        return sendResponse(false, 'Failed to retrieve user progress', null, Response::HTTP_INTERNAL_SERVER_ERROR);
      }  
    }
    public function storeOrUpdateUserProgress(UserProgressRequest $request): JsonResponse
    {
        try{
            $validated = $request->validated();
            $userProgress = $this->userProgressService->createOrUpdateUserProgress($validated);
            if (!$userProgress) {
                return response()->json([
                    'message' => 'Failed to create or update user progress',
                    'data' => null
                ], 500);
            }
            return sendResponse(true, 'User progress created successfully', $userProgress, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('UserProgress Create Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to create UserProgress', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
