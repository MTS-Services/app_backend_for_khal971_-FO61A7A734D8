<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\UserUpdatedRequest;
use App\Http\Services\UserService;
use App\Models\User;
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
    public function user(Request $request)
    {

        try {
            $user = $this->userService->getUser($request->user()->id);
            if (!$user) {
                return sendResponse(false, 'User not authenticated', null, Response::HTTP_UNAUTHORIZED);
            }
            return sendResponse(true, 'User details fetched successfully', $user, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('User Fetch Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to fetch user details', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get paginated user list (authenticated).
     */
    public function users($perPage = 10)
    {
        try {
            $users = $this->userService->getUsers()->paginate($perPage);
            return sendResponse(true, 'User list fetched successfully', $users, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('User List Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to fetch user list', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function updateUser(UserUpdatedRequest $request)
    {
        try{
            $user = $this->userService->getUser($request->user()->id);
            $file = $request->validated('image') && $request->hasFile('image') ? $request->file('image') : null;
            $validated = $request->validated();
            $user = $this->userService->updateUser($user, $validated, $file);
            return sendResponse(true, 'User updated successfully', $user, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('User Update Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to update user', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
