<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\UserItemProgressRequest;
use App\Http\Resources\UserItemProgressCollection;
use App\Http\Resources\UserItemProgressResource;
use App\Http\Services\UserItemProgressService;
use App\Models\UserItemProgresss;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UserItemProgressController extends Controller
// {
//     protected UserItemProgressService $service;

//     public function __construct(UserItemProgressService $service)
//     {
//         $this->service = $service;
//     }

//     public function index(Request $request)
//     {
//         $filters = $request->only([
//             'user_id', 'item_type', 'status', 'parent_progress_id'
//         ]);
//         $perPage = $request->get('per_page', 15);

//         return response()->json($this->service->list($filters, $perPage));
//     }

//     public function store(UserItemProgressRequest $request)
//     {
//        try{
//            $data = $request->validated();
//            $user_item_progress = $this->service->createUserItemProgress($data);
//            if (!$user_item_progress) {
//                return sendResponse(false, 'Failed to create user item progress', null, Response::HTTP_INTERNAL_SERVER_ERROR);
//            }
//            return sendResponse(true, 'User item progress created successfully', $user_item_progress, Response::HTTP_CREATED);
//        } catch (\Exception $e) {
//            Log::error('UserItemProgress Create Error: ' . $e->getMessage());
//            return sendResponse(false, 'Failed to create user item progress', null, Response::HTTP_INTERNAL_SERVER_ERROR);
//        }
//     }

//     public function show(UserItemProgresss $user_item_progress)
//     {
//         try{
//             $user_item_progress = $this->service->getUserItemProgress($user_item_progress->id);
//             if (!$user_item_progress) {
//                 return sendResponse(false, 'User item progress not found', null, Response::HTTP_NOT_FOUND);
//             }
//             return sendResponse(true, 'User item progress fetched successfully', $user_item_progress, Response::HTTP_OK);
//         } catch (\Exception $e) {
//             Log::error('UserItemProgress Fetch Error: ' . $e->getMessage());
//             return sendResponse(false, 'Failed to retrieve user item progress', null, Response::HTTP_INTERNAL_SERVER_ERROR);
//         }
//     }

//     public function update(UserItemProgressRequest $request, UserItemProgresss $user_item_progress)
//     {
//         try{ 
//             $data = $request->validated();
//             $user_item_progress = $this->service->updateUserItemProgress($user_item_progress, $data);
//             if (!$user_item_progress) {
//                 return sendResponse(false, 'Failed to update user item progress', null, Response::HTTP_INTERNAL_SERVER_ERROR);
//             }
//             return sendResponse(true, 'User item progress updated successfully', $user_item_progress, Response::HTTP_OK);
//         } catch (\Exception $e) {
//             Log::error('UserItemProgress Update Error: ' . $e->getMessage());
//             return sendResponse(false, 'Failed to update user item progress', null, Response::HTTP_INTERNAL_SERVER_ERROR);
//         }
//     }
// }



{
    private UserItemProgressService $progressService;

    public function __construct(UserItemProgressService $progressService)
    {
        $this->progressService = $progressService;
    }

    /**
     * Get user's item progress with pagination and filters.
     */
    public function index(Request $request): JsonResponse
    {
        $statusLabels = array_values(UserItemProgresss::getStatusLabels());

        $request->validate([
            'item_type' => 'nullable|string|in:question,lesson,video,quiz',
            'status' => 'nullable|string|in:' . implode(',', $statusLabels),
            'parent_progress_id' => 'nullable|integer|exists:user_progress,id',
            'is_bookmarked' => 'nullable|boolean',
            'is_flagged' => 'nullable|boolean',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $userId = Auth::id();
        $perPage = $request->input('per_page', 15);
        $filters = $request->only(['item_type', 'status', 'parent_progress_id', 'is_bookmarked', 'is_flagged']);

        // Convert status label to integer if provided
        if (isset($filters['status'])) {
            $statusMap = array_flip(UserItemProgresss::getStatusLabels());
            $filters['status'] = $statusMap[$filters['status']] ?? null;
        }

        $progress = $this->progressService->getUserItemProgress($userId, $filters, $perPage);

        return sendResponse(true, 'Progress fetched successfully', $progress, Response::HTTP_OK);
    }

    /**
     * Get specific user item progress.
     */
    public function show(UserItemProgresss $user_item_progress): JsonResponse
    {
        $userId = Auth::id();
        $item_type = $user_item_progress->item_type;
        $item_id = $user_item_progress->item_id;

        $progress = $this->progressService->getUserItemProgresss($userId, $item_type, $item_id);

        if (!$progress) {
            return sendResponse(false, 'Progress not found', null, Response::HTTP_NOT_FOUND);
        }

        return sendResponse(true, 'Progress fetched successfully', new UserItemProgressResource($progress), Response::HTTP_OK);
    }

    /**
     * Create or update user item progress.
     */
    public function store(UserItemProgressRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        try {
            $progress = $this->progressService->createOrUpdate($data);

            return response()->json([
                'success' => true,
                'message' => 'Progress saved successfully.',
                'data' => new UserItemProgressResource($progress),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save progress.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update existing progress.
     */
    public function update(UserItemProgressRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();

        try {
            $progress = $this->progressService->updateStatus($id, $data['status'], $data);

            return response()->json([
                'success' => true,
                'message' => 'Progress updated successfully.',
                'data' => new UserItemProgressResource($progress),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update progress.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Add time spent to progress.
     */
    public function addTimeSpent(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'seconds' => 'required|integer|min:1|max:86400', // Max 24 hours
        ]);

        try {
            $progress = $this->progressService->addTimeSpent($id, $request->input('seconds'));

            return response()->json([
                'success' => true,
                'message' => 'Time spent updated successfully.',
                'data' => new UserItemProgressResource($progress),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update time spent.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle bookmark status.
     */
    public function toggleBookmark(int $id): JsonResponse
    {
        try {
            $progress = $this->progressService->toggleBookmark($id);

            return response()->json([
                'success' => true,
                'message' => 'Bookmark toggled successfully.',
                'data' => new UserItemProgressResource($progress),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle bookmark.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle flag status.
     */
    public function toggleFlag(int $id): JsonResponse
    {
        try {
            $progress = $this->progressService->toggleFlag($id);

            return response()->json([
                'success' => true,
                'message' => 'Flag toggled successfully.',
                'data' => new UserItemProgressResource($progress),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle flag.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update notes for progress.
     */
    public function updateNotes(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'notes' => 'required|string|max:1000',
        ]);

        try {
            $progress = $this->progressService->updateNotes($id, $request->input('notes'));

            return response()->json([
                'success' => true,
                'message' => 'Notes updated successfully.',
                'data' => new UserItemProgressResource($progress),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update notes.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get progress statistics.
     */
    public function statistics(Request $request): JsonResponse
    {
        $request->validate([
            'item_type' => 'nullable|string|in:question,lesson,video,quiz',
            'parent_progress_id' => 'nullable|integer|exists:user_progress,id',
        ]);

        $userId = Auth::id();
        $filters = $request->only(['item_type', 'parent_progress_id']);

        $statistics = $this->progressService->getProgressStatistics($userId, $filters);

        return response()->json([
            'success' => true,
            'data' => $statistics,
        ]);
    }

    /**
     * Bulk update progress for multiple items.
     */
    public function bulkUpdate(UserItemProgressRequest $request): JsonResponse
    {
        $userId = Auth::id();
        $items = $request->validated()['items'];

        try {
            $updatedItems = $this->progressService->bulkUpdateProgress($userId, $items);

            return response()->json([
                'success' => true,
                'message' => 'Progress updated successfully for ' . $updatedItems->count() . ' items.',
                'data' => UserItemProgressResource::collection($updatedItems),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk update progress.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get progress for multiple items.
     */
    public function getMultipleItems(Request $request): JsonResponse
    {
        $request->validate([
            'items' => 'required|array|min:1|max:50',
            'items.*.type' => 'required|string|in:question,lesson,video,quiz',
            'items.*.id' => 'required|integer|min:1',
        ]);

        $userId = Auth::id();
        $items = $request->input('items');

        $progress = $this->progressService->getUserProgressForItems($userId, $items);

        return response()->json([
            'success' => true,
            'data' => UserItemProgressResource::collection($progress),
        ]);
    }

    /**
     * Get progress by parent progress ID.
     */
    public function getByParent(int $parentProgressId): JsonResponse
    {
        $progress = $this->progressService->getProgressByParent($parentProgressId);

        return response()->json([
            'success' => true,
            'data' => UserItemProgressResource::collection($progress),
        ]);
    }

    /**
     * Delete progress record.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->progressService->deleteProgress($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Progress record not found.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Progress deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete progress.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
