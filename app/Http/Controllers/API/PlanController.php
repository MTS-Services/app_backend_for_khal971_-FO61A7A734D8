<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\PlanRequest;
use App\Http\Services\PlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    protected PlanService $planService;

    public function __construct(PlanService $planService)
    {
        $this->planService = $planService;
    }

    public function index(): JsonResponse
    {
        $plans = $this->planService->getActivePlans();

        return response()->json([
            'success' => true,
            'data' => $plans
        ]);
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
