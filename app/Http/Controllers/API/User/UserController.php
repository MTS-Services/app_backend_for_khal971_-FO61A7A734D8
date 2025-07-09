<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\UserUpdatedRequest;
use App\Http\Resources\UserResource;
use App\Http\Services\UserService;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function user(Request $request): JsonResponse
    {

        try {
            $user = $this->userService->getUser($request->user()->id)->load(["userClass", "creater", "updater"]);
            if (!$user) {
                return sendResponse(false, 'User not authenticated', null, Response::HTTP_UNAUTHORIZED);
            }
            return sendResponse(true, 'User details fetched successfully', new UserResource($user), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('User Fetch Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to fetch user details', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get paginated user list (authenticated).
     */
    public function users(): JsonResponse
    {
        try {
            if (request()->user()->is_admin !== true) {
                return sendResponse(false, 'Unauthorized access', null, Response::HTTP_UNAUTHORIZED);
            }
            $users = $this->userService->getUsers()->with(['userClass', 'creater', 'updater'])->paginate(request()->get('per_page', 10));
            return sendResponse(true, 'User list fetched successfully', UserResource::collection($users), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('User List Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to fetch user list', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function updateUser(UserUpdatedRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->getUser($request->user()->id);
            $file = $request->validated('image') && $request->hasFile('image') ? $request->file('image') : null;
            $validated = $request->validated();
            $user = $this->userService->updateUser($user, $validated, $file);
            return sendResponse(true, 'User updated successfully', new UserResource($user), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('User Update Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to update user', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getUsers(Request $request): JsonResponse
    {
        $users = User::all();
        return sendResponse(true, 'Users fetched successfully', $users, Response::HTTP_OK);
    }
}
