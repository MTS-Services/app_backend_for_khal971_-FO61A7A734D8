<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\UserItemProgressRequest;
use App\Http\Services\UserItemProgressService;
use App\Models\UserItemProgresss;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class UserItemProgressController extends Controller
{
    protected UserItemProgressService $service;

    public function __construct(UserItemProgressService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = $request->only([
            'user_id', 'item_type', 'status', 'parent_progress_id'
        ]);
        $perPage = $request->get('per_page', 15);

        return response()->json($this->service->list($filters, $perPage));
    }

    public function store(UserItemProgressRequest $request)
    {
       try{
           $data = $request->validated();
           $user_item_progress = $this->service->createUserItemProgress($data);
           if (!$user_item_progress) {
               return sendResponse(false, 'Failed to create user item progress', null, Response::HTTP_INTERNAL_SERVER_ERROR);
           }
           return sendResponse(true, 'User item progress created successfully', $user_item_progress, Response::HTTP_CREATED);
       } catch (\Exception $e) {
           Log::error('UserItemProgress Create Error: ' . $e->getMessage());
           return sendResponse(false, 'Failed to create user item progress', null, Response::HTTP_INTERNAL_SERVER_ERROR);
       }
    }

    public function show(UserItemProgresss $user_item_progress)
    {
        try{
            $user_item_progress = $this->service->getUserItemProgress($user_item_progress->id);
            if (!$user_item_progress) {
                return sendResponse(false, 'User item progress not found', null, Response::HTTP_NOT_FOUND);
            }
            return sendResponse(true, 'User item progress fetched successfully', $user_item_progress, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('UserItemProgress Fetch Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to retrieve user item progress', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UserItemProgressRequest $request, UserItemProgresss $user_item_progress)
    {
        try{ 
            $data = $request->validated();
            $user_item_progress = $this->service->updateUserItemProgress($user_item_progress, $data);
            if (!$user_item_progress) {
                return sendResponse(false, 'Failed to update user item progress', null, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return sendResponse(true, 'User item progress updated successfully', $user_item_progress, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('UserItemProgress Update Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to update user item progress', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

