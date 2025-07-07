<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\UserProgressRequest;
use App\Http\Services\UserProgressService;
use App\Models\UserProgress;
use Illuminate\Http\Request;
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

    public function index(Request $request)
    {
        try {
            $query = $this->userProgressService->getUserProgress()->get();

            // Optional Filters
            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            if ($request->has('content_type')) {
                $query->where('content_type', $request->content_type);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('content_type', 'like', "%$search%")
                        ->orWhere('status', 'like', "%$search%");
                });
            }

            // Sorting
            $sortField = $request->get('sort_field', 'updated_at');
            $sortDirection = $request->get('sort_direction', 'desc');
            $query->orderBy($sortField, $sortDirection);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $progressList = $query->paginate($perPage);

            return sendResponse(true, 'User progress retrieved successfully', $progressList, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('UserProgress Fetch Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to retrieve user progress', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
        try {
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
