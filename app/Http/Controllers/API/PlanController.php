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
            $plans = $this->planService->getPlans()->get();
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
       try{
           $plan = $this->planService->getPlan($id);
           if(!$plan){
               return sendResponse(false, 'Plan not found', null, Response::HTTP_NOT_FOUND);
           }
           return sendResponse(true, 'Plan fetched successfully', new PlansResource($plan), Response::HTTP_OK);
       } catch (\Exception $e) {
           Log::error('Plan Error: ' . $e->getMessage());
           return sendResponse(false, 'Failed to fetch plan', null, Response::HTTP_INTERNAL_SERVER_ERROR);
       }
    }
    public function store(PlanRequest $request): JsonResponse
    {
        try{
            if (request()->user()->is_admin !== true) {
                return sendResponse(false, 'Unauthorized access', null, Response::HTTP_UNAUTHORIZED);
            }
            $validated = $request->validated();
            $plan = $this->planService->createPlan($validated);
            if(!$plan){
                return sendResponse(false, 'Failed to create plan', null, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return sendResponse(true, 'Plan created successfully', new PlansResource($plan), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Plan Create Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to create plan', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function update(PlanRequest $request, $id): JsonResponse
    {
        try{
            if (request()->user()->is_admin !== true) {
                return sendResponse(false, 'Unauthorized access', null, Response::HTTP_UNAUTHORIZED);
            }
            $plan = $this->planService->getPlan($id);
            if(!$plan){
                return sendResponse(false, 'Plan not found', null, Response::HTTP_NOT_FOUND);
            }
            $validated = $request->validated();
            $plan = $this->planService->updatePlan($plan, $validated);
            if(!$plan){
                return sendResponse(false, 'Failed to update plan', null, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return sendResponse(true, 'Plan updated successfully', new PlansResource($plan), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Plan Update Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to update plan', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function destroy($id): JsonResponse
    {
        try{
            if (request()->user()->is_admin !== true) {
                return sendResponse(false, 'Unauthorized access', null, Response::HTTP_UNAUTHORIZED);
            }
            $plan = $this->planService->getPlan($id);
            if(!$plan){
                return sendResponse(false, 'Plan not found', null, Response::HTTP_NOT_FOUND);
            }
            $plan = $this->planService->deletePlan($plan);
            return sendResponse(true, 'Plan deleted successfully', null, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Plan Delete Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to delete plan', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function toggleStatus($id): JsonResponse
    {
        try {
            if (request()->user()->is_admin !== true) {
                return sendResponse(false, 'Unauthorized access', null, Response::HTTP_UNAUTHORIZED);
            }
            $plan = $this->planService->getPlan($id);
            if (!$plan) {
                return sendResponse(false, 'Plan not found', null, Response::HTTP_NOT_FOUND);
            }
            $plan = $this->planService->toggleStatus($plan);
            return sendResponse(true, "Plan {$plan->status_label}  successfully",["status" => $plan->status_label],  Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Plan Status Toggle Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to toggle Plan status', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
