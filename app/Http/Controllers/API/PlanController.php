<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\PlanRequest;
use App\Http\Resources\PlansResource;
use App\Http\Services\PlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PlanController extends Controller
{
    protected PlanService $planService;

    public function __construct(PlanService $planService)
    {
        $this->planService = $planService;
    }

    public function index(): JsonResponse
    {
        try{
            $plans = $this->planService->getActivePlans()->get();
            if(!$plans){
                return sendResponse(false, 'Plan list not found', null, Response::HTTP_NOT_FOUND);
            }
            return sendResponse(true, 'Plan list fetched successfully', PlansResource::collection($plans), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Plan List Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to fetch plan list', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id): JsonResponse
    {
        $plan = $this->planService->getPlanById($id);

        if (!$plan) {
            return response()->json([
                'success' => false,
                'message' => 'Plan not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $plan
        ]);
    }
    public function store(PlanRequest $request): JsonResponse
    {
        $data = $request->validated();
        $plan = $this->planService->createPlan($data);

        return response()->json(['success' => true, 'data' => $plan], 201);
    }
}
