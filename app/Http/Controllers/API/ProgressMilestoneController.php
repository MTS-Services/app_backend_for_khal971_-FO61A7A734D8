<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\ProgressMilestoneRequest;
use App\Http\Services\ProgressMilestoneService;
use App\Models\ProgressMilestone;
use App\Models\ProgressMilestoneTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProgressMilestoneController extends Controller
{
    protected ProgressMilestoneService $milestoneService;

    public function __construct(ProgressMilestoneService $milestoneService)
    {
        $this->milestoneService = $milestoneService;
    }

    /**
     * Display a listing of the milestones
     */
    public function index(): JsonResponse
    {
        try {
            $milestones = $this->milestoneService->getProgressMilestones()->get();
            if (!$milestones) {
                return sendResponse(false, 'Milestones not found', null, Response::HTTP_NOT_FOUND);
            }
            return sendResponse(true, 'Milestones fetched successfully', $milestones, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Milestone Fetch Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to fetch milestones', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created milestone
     */
    public function store(ProgressMilestoneRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $milestone = $this->milestoneService->createMilestone($validated);
            if (!$milestone) {
                return sendResponse(false, 'Failed to create milestone', null, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return sendResponse(true, 'Milestone created successfully', $milestone, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Milestone Create Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to create milestone', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified milestone
     */
    public function show(ProgressMilestone $progress_milestone): JsonResponse
    {
        try {
            $progress_milestone = $this->milestoneService->getProgressMilestone($progress_milestone->id);
            if (!$progress_milestone) {
                return sendResponse(false, 'Milestone not found', null, Response::HTTP_NOT_FOUND);
            }
            return sendResponse(true, 'Milestone fetched successfully', $progress_milestone, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Milestone Fetch Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to fetch milestone', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified milestone
     */
    public function update(ProgressMilestoneRequest $request, ProgressMilestone $milestone): JsonResponse
    {

        try {
            $validated = $request->validated();
            $updatedMilestone = $this->milestoneService->updateMilestone($milestone, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Milestone updated successfully',
                'data' => $updatedMilestone,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update milestone',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified milestone (soft delete)
     */
    public function destroy(ProgressMilestone $milestone): JsonResponse
    {
        try {
            $this->milestoneService->deleteMilestone($milestone);

            return response()->json([
                'success' => true,
                'message' => 'Milestone deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete milestone',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hard delete the specified milestone
     */
    public function forceDelete(ProgressMilestone $milestone): JsonResponse
    {
        try {
            $this->milestoneService->hardDeleteMilestone($milestone);

            return response()->json([
                'success' => true,
                'message' => 'Milestone permanently deleted',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to permanently delete milestone',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reorder milestones
     */
    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'milestone_ids' => 'required|array',
            'milestone_ids.*' => 'required|integer|exists:progress_milestones,id',
        ]);

        try {
            $result = $this->milestoneService->reorderMilestones($validated['milestone_ids']);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Milestones reordered successfully',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to reorder milestones',
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reorder milestones',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get milestone statistics
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->milestoneService->getMilestoneStats();

            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve milestone statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get available content types and milestone types
     */
    public function options(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'content_types' => ProgressMilestoneTranslation::getContentTypes(),
                'milestone_types' => ProgressMilestoneTranslation::getMilestoneTypes(),
            ],
        ]);
    }

    /**
     * Check user milestone achievements
     */
    public function checkAchievements(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_progress' => 'required|array',
            'user_progress.*' => 'required|array',
        ]);

        try {
            $achievedMilestones = $this->milestoneService->getAchievedMilestones($validated['user_progress']);

            return response()->json([
                'success' => true,
                'data' => $achievedMilestones,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check milestone achievements',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
